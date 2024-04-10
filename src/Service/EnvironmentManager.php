<?php

// src/Service/EnvironmentManager.php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InstancesRepository;
use App\Repository\TasksRepository;

use App\Entity\Sessions;
use App\Entity\SessionStatuses;
use App\Entity\Tasks;
use App\Entity\Environments;
use App\Entity\EnvironmentStatuses;
use App\Entity\InstanceTypes;
use App\Entity\Instances;
//use App\Entity\InstanceStatuses;
//use App\Entity\Addresses;
use App\Service\AwxManager;
//use App\Service\LxcManager;
//use App\Service\SessionManager;
use Symfony\Component\Messenger\MessageBusInterface;
//use App\Message\SessionAction;
use App\Message\LxcOperation; 
use App\Message\EnvironmentAction;
use App\Message\EnvironmentEvent;

class EnvironmentManager
{
    private LoggerInterface $logger;

    private EntityManagerInterface $entityManager;
    private TasksRepository $taskRepository;
//    private $addressRepository;
    private InstancesRepository $instanceRepository;
//    private $instanceStatusesRepository;
    private $sessionStatusesRepository;
    private $sessionsRepository;
    private $environmentRepository;
    private $environmentStatusesRepository;

//    private $sessionBus;
    private $lxcOperationBus;
    private $environmentEventBus;
    private $environmentActionBus;

//    private LxcManager $lxcService;
    private $awxService;
//    private $sessionService;

    public function __construct( LoggerInterface $logger, EntityManagerInterface $em, 
	AwxManager $awx, //SessionManager $session,
            // MessageBusInterface $sessionBus, 
            MessageBusInterface $lxcOperationBus,
            MessageBusInterface $environmentEventBus, MessageBusInterface $environmentActionBus)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
//	$this->lxcService = $lxc;
//	$this->sessionBus = $sessionBus;
	$this->lxcOperationBus = $lxcOperationBus;
	$this->awxService = $awx;
        $this->environmentEventBus = $environmentEventBus;
        $this->environmentActionBus = $environmentActionBus;
//	$this->sessionService = $session;

        // get the repositories
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
//        $this->addressRepository = $this->entityManager->getRepository( Addresses::class);
        $this->instanceRepository = $this->entityManager->getRepository( Instances::class);
//        $this->instanceStatusesRepository = $this->entityManager->getRepository( InstanceStatuses::class);
        $this->environmentRepository = $this->entityManager->getRepository( Environments::class);
        $this->environmentStatusesRepository = $this->entityManager->getRepository( EnvironmentStatuses::class);
        $this->sessionStatusesRepository = $this->entityManager->getRepository( SessionStatuses::class);
        $this->sessionsRepository = $this->entityManager->getRepository( Sessions::class);
    }
/*
    public function createInstance(InstanceTypes $instance_type, bool $async = true): ?Instances {
        $this->logger->debug(__METHOD__);

        if ($async) {
            $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "createInstance",
                        "os" => $instance_type->getOs()->getAlias(),
                        "hp" => $instance_type->getHwProfile()->getName()]));
        } else {
            return $this->lxcService->createInstance($instance_type->getOs()->getAlias(),
                            $instance_type->getHwProfile()->getName());
        }
        return null;
    }
*/
    
    // Bind Instance to the Evironment
    public function bindInstance($environment, $instance) {
        $this->logger->debug(__METHOD__);

	$environment->setInstance($instance);
	$this->entityManager->flush();

        $this->deployEnvironment($environment);
    }
    
    // Allocate the Instance
    public function allocateInstance(InstanceTypes $it): ?Instances {
        $this->logger->debug(__METHOD__);

        // TODO: check input parameters
        // Find started instance
        $started_instance = $this->instanceRepository->findOneByTypeAndStatus($it->getId(), "Started");

        // Check if suitable instance has been found
        if ($started_instance) {
            $this->logger->debug('Suitable started instance has been found: ' . $started_instance);
            return $started_instance;
        }

        // Find stopped instance
        $stopped_instance = $this->instanceRepository->findOneByTypeAndStatus($it->getId(), "Stopped");

        // Check if suitable instance has been found
        if ($stopped_instance) {

            $this->logger->debug('Suitable stopped instance has been found: ' . $stopped_instance);

            // start instance
            $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "start",
                "name" => $stopped_instance->getName()]));
//            $this->lxcService->start($stopped_instance->getName(), false, false);

            return $stopped_instance;
        }

        return null;
    }

    public function deleteEnvironment($environment) {

        // Release instance
        $instance = $environment->getInstance();

        if ($instance) {
            $this->releaseInstance($instance);
        }
        $this->environmentRepository->remove($environment, true);
    }

    // Release the Instance
    public function releaseInstance(Instances $instance): bool {
        $this->logger->debug(__METHOD__);

        // TODO: check input parameters
        // TODO: restore init snapshot
        // Unbind an instance from env so it can be used again
        $instance->setEnvs(null);

        // Store item into the DB
        $this->entityManager->flush();

        // stop instance for the time being
//	$this->stopInstance($instance);
        
        $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "setInstanceStatus",
            "name" => $instance->getName(), "status" => $instance->getStatus()->getStatus()]));
        
//        $this->lxcService->setInstanceStatus($instance->getId(), 
//                $instance->getStatus()->getStatus());

        return true;
    }

    /*
    public function startInstance(Instances $instance, bool $async = true) {
        $this->logger->debug(__METHOD__);

        if ($async) {
            $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "startInstance",
                        "name" => $instance->getName()]));
        } else {
            $this->lxcService->start($instance->getName());
        }
    }

    public function restartInstance(Instances $instance, bool $async = true) {
        $this->logger->debug(__METHOD__);

        if ($async) {
            $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "restartInstance",
                        "name" => $instance->getName()]));
        } else {
            $this->lxcService->stopObject($instance->getName());
        }
    }

    public function stopInstance(Instances $instance, bool $async = true) {
        $this->logger->debug(__METHOD__);

        if ($async) {
            $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "stopInstance",
                        "name" => $instance->getName()]));
        } else {
            $this->lxcService->restartObject($instance->getName());
        }
    }

    public function deleteInstance(Instances $instance) {
        $this->logger->debug(__METHOD__);

        $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "deleteInstance",
                    "name" => $instance->getName()]));
    }
    
    public function deleteAllInstances() {
        $this->logger->debug(__METHOD__);

        $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "deleteAllInstances"]));
    }

    public function setInstanceStatus(int $instance_id, $status_str): bool {

        $this->logger->debug(__METHOD__);

        $status = $this->instanceStatusesRepository->findOneByStatus($status_str);

        if (!$status) {
            $this->logger->debug('No such instance status: ' . $status_str);
            return false;
        }

        $instance = $this->instanceRepository->findOneById($instance_id);

        if (!$instance) {
            $this->logger->debug('No such instance!');
            return false;
        }

        // Special statuses for bound instances
        $target_status = $this->tweakInstanceStatus($instance_id, $status_str);

        $this->logger->debug('Changing instance ' . $instance . ' status to: ' . $target_status);

        $instance->setStatus($target_status);

        // Store item into the DB
//	  $this->entityManager->persist($instance);
        $this->entityManager->flush();

        return true;
    }
*/
    /*
    private function tweakInstanceStatus(int $instance_id, string $status_str): InstanceStatuses {

        $this->logger->debug(__METHOD__);

        $instance = $this->instanceRepository->findOneById($instance_id);

        $status = $this->instanceStatusesRepository->findOneByStatus($status_str);
        
        // Special statuses for bound instances
        $envs = $instance->getEnvs();
        if ($envs) {
            if ($status_str == "Started") {
                $status = $this->instanceStatusesRepository->findOneByStatus("Running");
            } elseif ($status_str == "Stopped") {
                $status = $this->instanceStatusesRepository->findOneByStatus("Sleeping");
            }
        } else {
            if ($status_str == "Running") {
                $status = $this->instanceStatusesRepository->findOneByStatus("Started");
            } elseif ($status_str == "Sleeping") {
                $status = $this->instanceStatusesRepository->findOneByStatus("Stopped");
            }
        }
        return $status;
    }
*/
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

/*
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
*/

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
/*

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
*/
    public function createEnvironment(int $task_id, int $session_id = -1, 
            bool $async = true): ?Environments {
        $this->logger->debug(__METHOD__);

        // TODO: check input parameters

        if ($async) {
            $this->environmentActionBus->dispatch(new EnvironmentAction(["action" => "create",
                        "task_id" => $task_id, "session_id" => $session_id]));
            return null;
        }

        $task = $this->taskRepository->findOneById($task_id);
        $session = null;
        if ($session_id>0) {
            $session = $this->sessionsRepository->findOneById($session_id);
        }

        // Get the suitable InstanceType for a task
        $instance_type = $this->findSuitableInstanceType($task);

        if (!$instance_type) {
            $this->logger->debug('No suitable instance types are available for task: ' . $task);
            return null;
        }
        $this->logger->debug('First suitable instance type: ' . $instance_type);

        $env = new Environments;

        $env_status = $this->environmentStatusesRepository->findOneByStatus("New");
        $env->setStatus($env_status);

        $env->setTask($task);
        $env->setSession($session);

        // Store item into the DB
        $this->entityManager->persist($env);
//            $this->entityManager->flush();

        $this->logger->debug('Environment `' . $env . '` was created.');
        
        $this->environmentEventBus->dispatch(new EnvironmentEvent(["event" => "created", 
            "id" => $env->getId()]));

        $this->entityManager->flush();

//	  $timestamp = new \DateTimeImmutable('NOW');
//	  $env->setHash(substr(md5($timestamp->format('Y-m-d H:i:s')),0,8));
//	  $name = $this->createInstance($instance_type);
	  
        // Try to allocate the inctance first
        $instance = $this->allocateInstance($instance_type);
        
        if ($instance) {
            $env->setInstance($instance);

            // Store item into the DB
//	  $this->entityManager->persist($env);
            $this->entityManager->flush();

            $this->logger->debug('Instance `' . $instance->getName() .
                    '` has been allocated to the newly created environment.');

            // Deploy it immediately
            $this->deployEnvironment($env);

        } else {
            
            $this->logger->debug('Request instance creation for the newly created environment.');

//            $this->lxcService->create($instance_type->getOs()->getAlias(),
//                    $instance_type->getHwProfile()->getName(), $env);
            
            $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "create",
                "os" => $instance_type->getOs()->getAlias(),
                "hp" => $instance_type->getHwProfile()->getName(), 
                "env_id" => $env->getId()]));
        }

        return $env;
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
	  $result = $this->awxService->runJobTemplate($env->getTask()->getVerify(), $body);

	  $this->logger->debug('Status: ' . $result->status);
#	  $this->logger->debug('Status: ' . (($result->status == "successful")?1:0));
#	  $this->logger->debug('Status: ' . ($result->status == "successful")?1:0);
#	  $this->logger->debug('Status: ' . strcmp($result->status,"successful"));
	
	  $env->setValid((($result->status == "successful")?true:false));
	  $env->setVerification($result->id);
	  $this->entityManager->flush();

	  $this->setEnvironmentStatus($env, "Verified");

	  // Release the Instance
//	  $this->releaseInstance($env->getInstance());

	  return true;

	} else {

	  $this->logger->debug('Verify job template with id `' . $task_id . '` was NOT found.');
	}

	// Release the Instance
//	$this->releaseInstance($env->getInstance());

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
	  $this->awxService->runJobTemplate($env->getTask()->getSolve(), $body);

//	  $this->logger->debug('Status: ' . $result->status);

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
//          $this->awxService->runJobTemplate(55, $body);

	  // Deploy actual environment
	  $this->awxService->runJobTemplate($env->getTask()->getDeploy(), $body);
/*
	  $env->setDeployment($result->id);
	  $this->entityManager->flush();

	  $this->logger->debug('Status: ' . $result->status);
*/
	  $this->setEnvironmentStatus($env, "Deployed");

	  return true;

	} else {

	  $this->logger->debug('Deploy job template with id `' . $task_id . '` was NOT found.');
	}

	return false;
    }

/*

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
*/
    public function findSuitableInstanceType(Tasks $task): ?InstanceTypes
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

