<?php

// src/Service/SessionManager.php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Sessions;
use App\Entity\SessionStatuses;
use App\Entity\Tasks;
use App\Entity\Environments;
use App\Entity\EnvironmentStatuses;
use App\Entity\InstanceTypes;
use App\Entity\Instances;
use App\Entity\InstanceStatuses;
use App\Entity\Addresses;
use App\Service\AwxManager;
use App\Service\LxcManager;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\SessionAction;

class SessionManager
{
    private $logger;

    private $entityManager;
    private $taskRepository;
    private $addressRepository;
    private $instanceRepository;
    private $instanceStatusesRepository;
    private $sessionStatusesRepository;
    private $environmentRepository;
    private $environmentStatusesRepository;

    private $bus;

    private $lxd;
    private $awx;

    public function __construct( LoggerInterface $logger, EntityManagerInterface $em, 
	LxcManager $lxd, AwxManager $awx, MessageBusInterface $bus)

    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
	$this->lxd = $lxd;
	$this->bus = $bus;
	$this->awx = $awx;

        // get the repositories
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
        $this->addressRepository = $this->entityManager->getRepository( Addresses::class);
        $this->instanceRepository = $this->entityManager->getRepository( Instances::class);
        $this->instanceStatusesRepository = $this->entityManager->getRepository( InstanceStatuses::class);
        $this->environmentRepository = $this->entityManager->getRepository( Environments::class);
        $this->environmentStatusesRepository = $this->entityManager->getRepository( EnvironmentStatuses::class);
        $this->sessionStatusesRepository = $this->entityManager->getRepository( SessionStatuses::class);
    }

//    public function createInstance(InstanceTypes $it, Environments $env = null): Instances
    public function createInstance(InstanceTypes $it): Instances
    {
        $this->logger->debug(__METHOD__);

	// TODO: check input parameters

	// Find an address item which is NOT linked to any instance
	$address = $this->addressRepository->findOneByInstance(null);
	
	// TODO: Handle situation when no such addresses found

	$this->logger->debug( "Selected address: " . $address->getIp() . ", MAC: " . $address->getMac());

	$name = $this->lxd->createInstance($it->getOs()->getAlias(), $it->getHwProfile()->getName(), $address->getMac());

	$this->logger->debug('Instance `' . $name . '` was created.');

	$instance = new Instances;
	$instance->setName($name);

	// It is New/Started by sefault
	$instance_status = $this->instanceStatusesRepository->findOneByStatus("Started");
	$instance->setStatus($instance_status);
//	Ca not use this function yet - entity is absent in the DB
//	$instance->setInstanceStatus("Bound");

	$instance->setInstanceType($it);
//	$now = new \DateTimeImmutable('NOW');
//	$instance->setCreatedAt($now);

	$address->setInstance($instance);

	// Store item into the DB
	$this->entityManager->persist($instance);
	$this->entityManager->flush();

	// Update Instance status
//	$this->setInstanceStatus($instance, "Bound");

	return $instance;
    }



    // Bind the Instance
    public function bindInstance(InstanceTypes $it): Instances
    {
        $this->logger->debug(__METHOD__);

	// TODO: check input parameters

	$instance = $this->instanceRepository->findOneByTypeAndStatus($it, "Started");

	// Check if suitable instance has been found
	if($instance)

	  $this->logger->debug('Suitable started instance has been found: '.$instance);

	// Create new Instance
	else
	    $instance = $this->createInstance($it);

	// Update Instance status
	$this->setInstanceStatus($instance, "Bound");

	return $instance;
    }



    // Release the Instance
    public function releaseInstance(Instances $instance): bool
    {
        $this->logger->debug(__METHOD__);

	// TODO: check input parameters

	// TODO: restore init snapshot

	// Update Instance status
	$this->setInstanceStatus($instance, "Started");

	return true;
    }

    public function setSessionStatus(Sessions $session, $status_str): bool
    {
        $this->logger->debug(__METHOD__);

	$status = $this->sessionStatusesRepository->findOneByStatus($status_str);

	if($status) {

	  $this->logger->debug('Changing session status to: '.$status);

	  $session->setStatus($status);

	  // Store item into the DB
	  $this->entityManager->persist($session);
	  $this->entityManager->flush();

	  return true;

	} else {

	  $this->logger->debug('No such session status: '.$status_str);

	  return false;
	}
    }

    public function setEnvironmentStatus(Environments $environment, $status_str): bool
    {
        $this->logger->debug(__METHOD__);

	$status = $this->environmentStatusesRepository->findOneByStatus($status_str);

	if($status) {

	  $this->logger->debug('Changing environment status to: '.$status);

	  $environment->setStatus($status);

	  // Store item into the DB
	  $this->entityManager->persist($environment);
	  $this->entityManager->flush();

	  return true;

	} else {

	  $this->logger->debug('No such environment status: '.$status_str);

	  return false;
	}
    }

    public function setInstanceStatus(Instances $instance, $status_str): bool
    {
        $this->logger->debug(__METHOD__);

	$status = $this->instanceStatusesRepository->findOneByStatus($status_str);

	if($status) {

	  $this->logger->debug('Changing instance '.$instance.' status to: '.$status);

	  $instance->setStatus($status);

	  // Store item into the DB
	  $this->entityManager->persist($instance);
	  $this->entityManager->flush();

	  return true;

	} else {

	  $this->logger->debug('No such instance status: '.$status_str);

	  return false;
	}
    }


    public function allocateEnvironment(Sessions $session): bool
    {
        $this->logger->debug(__METHOD__);

	// TODO: check input parameters
	$task = $this->getNextTask($session);

	$this->logger->debug( "Selected task: " . $task);

	$environment = $this->environmentRepository->findOneDeployed($session);

	// Environment has been found
	if($environment) {

	  $environment->setSession($session);

	  // Store item into the DB
	  $this->entityManager->persist($environment);
	  $this->entityManager->flush();

	  $this->logger->debug( "Allocated environment: " . $environment);

	// No env to allocate, create it
	} else {

	  $this->bus->dispatch(new SessionAction(["action" => "createEnvironment", "session_id" => $session->getId()]));

	  return false;	
	}

	return true;	
    }


    public function createEnvironment(Tasks $task, Sessions $session = null): ?Environments
    {
        $this->logger->debug(__METHOD__);

	// TODO: check input parameters

	// Get the suitable InstanceType for a task
	$instance_type = $this->getFirstInstanceType($task);

	if($instance_type) { 

	  $this->logger->debug('First suitable instance type: '.$instance_type);

	  $env = new Environments;

	  $env_status = $this->environmentStatusesRepository->findOneByStatus("Created");
	  $env->setStatus($env_status);

	  $env->setTask($task);
	  $env->setSession($session);

	  // Store item into the DB
	  $this->entityManager->persist($env);
	  $this->entityManager->flush();

	  $this->logger->debug('Environment `' . $env . '` was created.');

//	  $timestamp = new \DateTimeImmutable('NOW');
//	  $env->setHash(substr(md5($timestamp->format('Y-m-d H:i:s')),0,8));

//	  $name = $this->createInstance($instance_type);
	  $name = $this->bindInstance($instance_type);

	  $env->setInstance($name);

	  // Store item into the DB
	  $this->entityManager->persist($env);
	  $this->entityManager->flush();

	  $this->logger->debug('Instance `' . $name . '` has been bound to the environment.');

//	  $this->setEnvironmentStatus($env, "Created");

	  return $env;

	} else

	  $this->logger->debug('No suitable instance types are available for task: '.$task);

	return null;
    }



    public function verifyEnvironment(Environments $env): bool
    {
        $this->logger->debug(__METHOD__);

	$this->logger->debug('Verifying: ' . $env);

	if($task_id = $env->getTask()->getVerify()) {

	  // Limit execution on single host only
	  $body["limit"] = $env->getInstance()->getName();

	  // return the the account api
	  $result = $this->awx->runJobTemplate($env->getTask()->getVerify(), $body);

	  $this->logger->debug('Status: ' . $result->status);

	  $this->setEnvironmentStatus($env, "Verified");

	  return true;

	} else {

	  $this->logger->debug('Verify job template with id `' . $task_id . '` was NOT found.');
	}

	return false;
    }



    public function solveEnvironment(Environments $env): bool
    {
        $this->logger->debug(__METHOD__);

	$this->logger->debug('Solving: ' . $env);

	if($task_id = $env->getTask()->getSolve()) {

	  // Limit execution on single host only
	  $body["limit"] = $env->getInstance()->getName();

	  // return the the account api
	  $result = $this->awx->runJobTemplate($env->getTask()->getSolve(), $body);

	  $this->logger->debug('Status: ' . $result->status);

	  $this->setEnvironmentStatus($env, "Solved");

	  return true;

	} else {

	  $this->logger->debug('Deploy job template with id `' . $task_id . '` was NOT found.');
	}

	return false;
    }



    public function deployEnvironment(Environments $env): bool
    {
        $this->logger->debug(__METHOD__);

	$this->logger->debug('Deploying: ' . $env);

	if($task_id = $env->getTask()->getDeploy()) {

	  // Limit execution on single host only
	  $body["limit"] = $env->getInstance()->getName();

	  // return the the account api
	  $result = $this->awx->runJobTemplate($env->getTask()->getDeploy(), $body);

	  $this->logger->debug('Status: ' . $result->status);

	  $this->setEnvironmentStatus($env, "Deployed");

	  return true;

	} else {

	  $this->logger->debug('Deploy job template with id `' . $task_id . '` was NOT found.');
	}

	return false;
    }



    public function getRandomTask(): Tasks
    {
        $this->logger->debug(__METHOD__);

        $tasks = $this->taskRepository->findAll();

        return $tasks[rand(0,count($tasks)-1)];
    }



    public function getNextTask( Sessions $session): Tasks
    {
        $this->logger->debug(__METHOD__);


	// TODO: Select a task specifically for a session

        return $this->getRandomTask();
    }

    public function getFirstInstanceType(Tasks $task): ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

	$instanceTypes = $task->getTaskInstanceTypes();

	if(count($instanceTypes))

          return $task->getTaskInstanceTypes()[0]->getInstanceType();

	else

	  return NULL;
    }

}

