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

class SessionManager
{
    private $logger;

    private $entityManager;
    private $taskRepository;
    private $addressRepository;
    private $instanceStatusesRepository;
    private $sessionStatusesRepository;
    private $environmentStatusesRepository;

    private $lxd;
    private $awx;

    public function __construct( LoggerInterface $logger, EntityManagerInterface $em, 
	LxcManager $lxd, AwxManager $awx)
    {
        $this->logger = $logger;
        $this->entityManager = $em;
	$this->lxd = $lxd;
	$this->awx = $awx;

        // get the repositories
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
        $this->addressRepository = $this->entityManager->getRepository( Addresses::class);
        $this->instanceStatusesRepository = $this->entityManager->getRepository( InstanceStatuses::class);
        $this->environmentStatusesRepository = $this->entityManager->getRepository( EnvironmentStatuses::class);
        $this->sessionStatusesRepository = $this->entityManager->getRepository( SessionStatuses::class);
    }

//    public function createInstance(InstanceTypes $it, Environments $env = null): Instances
    public function createInstance(InstanceTypes $it): Instances
    {
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
	$instance_status = $this->instanceStatusesRepository->findOneByStatus("Bound");
	$instance->setStatus($instance_status);

	$instance->setInstanceType($it);
	$now = new \DateTimeImmutable('NOW');
	$instance->setCreatedAt($now);

	$address->setInstance($instance);

	// Store item into the DB
	$this->entityManager->persist($instance);
	$this->entityManager->flush();

	// Update Instance status
//	$this->setInstanceStatus($instance, "Bound");

	return $instance;
    }

    public function setSessionStatus(Sessions $session, $status_str): bool
    {
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
	$status = $this->instanceStatusesRepository->findOneByStatus($status_str);

	if($status) {

	  $this->logger->debug('Changing instance status to: '.$status);

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

    public function createEnvironment(Tasks $task, Sessions $session = null): ?Environments
    {
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

	  $timestamp = new \DateTimeImmutable('NOW');
	  $env->setHash(substr(md5($timestamp->format('Y-m-d H:i:s')),0,8));

	  $name = $this->createInstance($instance_type);

	  $env->setInstance($name);

	  // Store item into the DB
	  $this->entityManager->persist($env);
	  $this->entityManager->flush();

	  $this->logger->debug('Environment `' . $env . '` was created.');

//	  $this->setEnvironmentStatus($env, "Created");

	  return $env;

	} else

	  $this->logger->debug('No suitable instance types are available for task: '.$task);

	return null;
    }

    public function deployEnvironment(Environments $env): bool
    {
	$this->logger->debug('Deploying: ' . $env);

	if($task_id = $env->getTask()->getDeploy()) {

	  // Limit execution on single host only
	  $body["limit"] = $env->getInstance()->getName();

	  // return the the account api
	  $result = $this->awx->deploy($env->getTask()->getDeploy(), $body);

	  $this->logger->debug('Status: ' . $result->status);

	  $this->setEnvironmentStatus($env, "Deployed");
/*
	  $env_status = $this->environmentStatusesRepository->findOneByStatus("Deployed");
	  $env->setStatus($env_status);

	  // Store item into the DB
	  $this->entityManager->persist($env);
	  $this->entityManager->flush();
*/
	  return true;

	} else {

	  $this->logger->debug('Deploy job template with id `' . $task_id . '` was NOT found.');
	}

	return false;
    }

    public function getRandomTask(): Tasks
    {
        $tasks = $this->taskRepository->findAll();

        return $tasks[rand(0,count($tasks)-1)];
    }

    public function getNextTask( Sessions $session): Tasks
    {

	// TODO: Select a task specifically for a session

        return $this->getRandomTask();
    }

    public function getFirstInstanceType(Tasks $task): ?InstanceTypes
    {  
	$instanceTypes = $task->getTaskInstanceTypes();

	if(count($instanceTypes))

          return $task->getTaskInstanceTypes()[0]->getInstanceType();

	else

	  return NULL;
    }

}

