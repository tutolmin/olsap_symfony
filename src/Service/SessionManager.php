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
use App\Message\LxcOperation;

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

    private $sessionBus;
    private $lxdBus;

    private $lxd;
    private $awx;

    public function __construct( LoggerInterface $logger, EntityManagerInterface $em, 
	LxcManager $lxd, AwxManager $awx, MessageBusInterface $sessionBus, MessageBusInterface $lxdBus)

    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
	$this->lxd = $lxd;
	$this->sessionBus = $sessionBus;
	$this->lxdBus = $lxdBus;
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
//	Can not use this function yet - entity is absent in the DB
//	$instance->setInstanceStatus("Bound");

	$instance->setInstanceType($it);
//	$now = new \DateTimeImmutable('NOW');
//	$instance->setCreatedAt($now);

	$address->setInstance($instance);

	// Store item into the DB
	$this->entityManager->persist($instance);
	$this->entityManager->flush();

	return $instance;
    }



    // Bind the Instance
    public function bindInstance(InstanceTypes $it): Instances
    {
        $this->logger->debug(__METHOD__);

	// TODO: check input parameters

	$instance = $this->instanceRepository->findOneByTypeAndStatus($it, "Started");

	// Check if suitable instance has been found
	if ($instance) {
            $this->logger->debug('Suitable started instance has been found: ' . $instance);
        }

        // Try to find stopped instance
        else {

            // Find stopped instance
            $instance = $this->instanceRepository->findOneByTypeAndStatus($it, "Stopped");

            // Check if suitable instance has been found
            if ($instance) {

                $this->logger->debug('Suitable stopped instance has been found: ' . $instance);

                // stop instance for the time being
                $this->startInstance($instance);

                // Create new Instance
            } else {
                $instance = $this->createInstance($it);
            }
        }

        // Update Instance status
	$this->setInstanceStatus($instance, "Running");

	return $instance;
    }



    // Release the Instance
    public function releaseInstance(Instances $instance): bool
    {
        $this->logger->debug(__METHOD__);

	// TODO: check input parameters

	// TODO: restore init snapshot

	// Unbind an instance from env so it can be used again
	$instance->setEnvs(null);

	// Store item into the DB
//	$this->entityManager->persist($session);
	$this->entityManager->flush();

	// Update Instance status
//	$this->setInstanceStatus($instance, "Started");

	// stop instance for the time being
	$this->stopInstance($instance);

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
//	  $this->entityManager->persist($session);
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
//	  $this->entityManager->persist($environment);
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
//	  $this->entityManager->persist($instance);
	  $this->entityManager->flush();

	  return true;

	} else {

	  $this->logger->debug('No such instance status: '.$status_str);

	  return false;
	}
    }



    public function setSessionTimestamp(Sessions $session, $timestamp_str): bool
    {
        $this->logger->debug(__METHOD__);

	// We will use it for any timestamp type
	$timestamp = new \DateTimeImmutable('NOW');
	
	// Which timestamp we are going to set
	switch($timestamp_str) {

	// Session started
	case "started":

	  // Only update the timestamp if it was not previously set
	  if(!$session->getStartedAt()) {

	    $session->setStartedAt($timestamp);
	    $this->entityManager->flush();
	  }
	  break;

	// Session finished
	case "finished":

	  // Only update the timestamp if it was not previously set
	  if(!$session->getFinishedAt()) {

	    $session->setFinishedAt($timestamp);
	    $this->entityManager->flush();
	  }
	  break;

	default:
	  $this->logger->debug('No such session timestamp: '.$timestamp_str);
	  break;
	}
	return true;
    }


    public function setEnvironmentTimestamp(Environments $environment, $timestamp_str): bool
    {
        $this->logger->debug(__METHOD__);

	// We will use it for any timestamp type
	$timestamp = new \DateTimeImmutable('NOW');
	
	// Which timestamp we are going to set
	switch($timestamp_str) {

	// Environment started
	case "started":

	  // Only update the timestamp if it was not previously set
	  if(!$environment->getStartedAt()) {

	    $environment->setStartedAt($timestamp);
	    $this->entityManager->flush();
	  }
	  break;

	// Environment skipped/finished
	case "skipped":
	case "verified":
	case "finished":

	  // Only update the timestamp if it was not previously set
	  if(!$environment->getFinishedAt()) {

	    $environment->setFinishedAt($timestamp);
	    $this->entityManager->flush();
	  }
	  break;

	default:
	  $this->logger->debug('No such environment timestamp: '.$timestamp_str);
	  break;
	}
	return true;
    }


    public function allocateEnvironment(Sessions $session): bool
    {
        $this->logger->debug(__METHOD__);

	// TODO: check input parameters
	$task = $this->getNextTask($session);

	$this->logger->debug( "Selected task: " . $task);

//	$environment = $this->environmentRepository->findOneDeployed($task->getId());
	$environments = $this->environmentRepository->findAllDeployed($task->getId());
	$environment = NULL;
	if (count($environments)) {
            $environment = $environments[0];
        }

        // Environment has been found
	if($environment) {

	  $environment->setSession($session);

	  // Store item into the DB
//	  $this->entityManager->persist($environment);
	  $this->entityManager->flush();

	  $this->logger->debug( "Allocated environment: " . $environment);

	// No env to allocate, create it
	} else {

	  $this->logger->debug( "No suitable envs found. Requesting new env creation.");

	  $this->sessionBus->dispatch(new SessionAction(["action" => "createEnvironment", 
		"session_id" => $session->getId(), "task_id" => $task->getId()]));

	  return false;	
	}

	return true;	
    }



    public function startInstance(Instances $instance)
    {
        $this->logger->debug(__METHOD__);

	$this->lxdBus->dispatch(new LxcOperation(["command" => "start", 
	  "instance_id" => $instance->getId()]));

	// Select which status to apply
	switch($instance->getStatus()) {

	case "Sleeping":	
	  $this->setInstanceStatus($instance, "Running");
	  break;

	default:
	  $this->setInstanceStatus($instance, "Started");
	  break;
	}
    }



    public function stopInstance(Instances $instance)
    {
        $this->logger->debug(__METHOD__);

	$this->lxdBus->dispatch(new LxcOperation(["command" => "stop", 
	  "instance_id" => $instance->getId()]));

	// Select which status to apply
	switch($instance->getStatus()) {

	case "Running":	
	  $this->setInstanceStatus($instance, "Sleeping");
	  break;

	default:
	  $this->setInstanceStatus($instance, "Stopped");
	  break;
	}
    }



    public function createEnvironment(Tasks $task, Sessions $session = null): ?Environments
    {
        $this->logger->debug(__METHOD__);

	// TODO: check input parameters

	// Get the suitable InstanceType for a task
	$instance_type = $this->getFirstInstanceType($task);

	if ($instance_type) {

            $this->logger->debug('First suitable instance type: ' . $instance_type);

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
//	  $this->entityManager->persist($env);
            $this->entityManager->flush();

            $this->logger->debug('Instance `' . $name . '` has been bound to the environment.');

//	  $this->setEnvironmentStatus($env, "Created");

            return $env;
        } else {
            $this->logger->debug('No suitable instance types are available for task: ' . $task);
        }

        return null;
    }



    public function verifyEnvironment(Environments $env): bool
    {
        $this->logger->debug(__METHOD__);

	$this->logger->debug('Verifying: ' . $env);

        $task_id = $env->getTask()->getVerify();
	if($task_id) {

	  // Limit execution on single host only
	  $body["limit"] = $env->getInstance()->getName();

	  // return the the account api
	  $result = $this->awx->runJobTemplate($env->getTask()->getVerify(), $body);

	  $this->logger->debug('Status: ' . $result->status);
#	  $this->logger->debug('Status: ' . (($result->status == "successful")?1:0));
#	  $this->logger->debug('Status: ' . ($result->status == "successful")?1:0);
#	  $this->logger->debug('Status: ' . strcmp($result->status,"successful"));
	
	  $env->setValid((($result->status == "successful")?true:false));
	  $env->setVerification($result->id);
	  $this->entityManager->flush();

	  $this->setEnvironmentStatus($env, "Verified");

	  // Release the Instance
	  $this->releaseInstance($env->getInstance());

	  return true;

	} else {

	  $this->logger->debug('Verify job template with id `' . $task_id . '` was NOT found.');
	}

	// Release the Instance
	$this->releaseInstance($env->getInstance());

	return false;
    }



    public function solveEnvironment(Environments $env): bool
    {
        $this->logger->debug(__METHOD__);

	$this->logger->debug('Solving: ' . $env);

        $task_id = $env->getTask()->getSolve();
	if($task_id) {

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

        $task_id = $env->getTask()->getDeploy();
	if($task_id) {

	  // Limit execution on single host only
	  $body["limit"] = $env->getInstance()->getName();

	  // Deploy test user credentials
          $this->awx->runJobTemplate(55, $body);

	  // Deploy actual environment
	  $result = $this->awx->runJobTemplate($env->getTask()->getDeploy(), $body);

	  $env->setDeployment($result->id);
	  $this->entityManager->flush();

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

	if (count($instanceTypes)) {
            return $task->getTaskInstanceTypes()[0]->getInstanceType();
        } else {
            return NULL;
        }
    }

}

