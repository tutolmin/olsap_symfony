<?php

// src/Service/SessionManager.php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SessionStatusesRepository;
use App\Repository\SessionsRepository;
use App\Repository\TasksRepository;
use App\Repository\EnvironmentsRepository;

use App\Entity\Sessions;
use App\Entity\SessionStatuses;
use App\Entity\Tasks;
use App\Entity\Environments;
//use App\Entity\EnvironmentStatuses;
//use App\Entity\InstanceTypes;
//use App\Entity\Instances;
//use App\Entity\InstanceStatuses;
//use App\Entity\Addresses;
//ctuse App\Service\AwxManager;
//use App\Service\LxcManager;
//use App\Service\EnvironmentManager;
use Symfony\Component\Messenger\MessageBusInterface;
//use App\Message\SessionAction;
use App\Message\EnvironmentAction;

class SessionManager
{
    private LoggerInterface $logger;

    private EntityManagerInterface $entityManager;
    private TasksRepository $taskRepository;
//    private $addressRepository;
//    private InstancesRepository $instanceRepository;
//    private $instanceStatusesRepository;
    private SessionStatusesRepository $sessionStatusesRepository;
    private SessionsRepository $sessionsRepository;
    private EnvironmentsRepository $environmentRepository;
//    private $environmentStatusesRepository;

    /**
     * 
     * @var array<string>
     */
    private $params;

//    private $sessionBus;
    private MessageBusInterface $environmentActionBus;

//    private $lxdService;
//    private $awxService;
//    private $envService;

    public function __construct( LoggerInterface $logger, EntityManagerInterface $em, 
//	AwxManager $awx, 
//            MessageBusInterface $sessionBus, 
            MessageBusInterface $environmentActionBus,
            int $app_start_envs)

    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
//	$this->lxdService = $lxd;
//	$this->sessionBus = $sessionBus;
//	$this->lxdOperationBus = $lxdOperationBus;
//	$this->awxService = $awx;
//	$this->envService = $env;

        $this->params['app_start_envs']        = strval($app_start_envs);

        $this->environmentActionBus = $environmentActionBus;

        // get the repositories
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
//        $this->addressRepository = $this->entityManager->getRepository( Addresses::class);
//        $this->instanceRepository = $this->entityManager->getRepository( Instances::class);
//        $this->instanceStatusesRepository = $this->entityManager->getRepository( InstanceStatuses::class);
        $this->environmentRepository = $this->entityManager->getRepository( Environments::class);
//        $this->environmentStatusesRepository = $this->entityManager->getRepository( EnvironmentStatuses::class);
        $this->sessionStatusesRepository = $this->entityManager->getRepository( SessionStatuses::class);
        $this->sessionsRepository = $this->entityManager->getRepository( Sessions::class);
    }
/*
    public function createInstance(InstanceTypes $instance_type, bool $async = true): ?Instances {
        $this->logger->debug(__METHOD__);

        if ($async) {
            $this->lxdOperationBus->dispatch(new LxcOperation(["command" => "createInstance",
                        "os" => $instance_type->getOs()->getAlias(),
                        "hp" => $instance_type->getHwProfile()->getName()]));
        } else {
            return $this->lxdService->createInstance($instance_type->getOs()->getAlias(),
                            $instance_type->getHwProfile()->getName());
        }
        return null;
    }
 */
/*
    // Bind the Instance
    public function bindInstance(InstanceTypes $it): Instances
    {
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

            // start instance asyncroneously
            $this->startInstance($stopped_instance, false);

            return $stopped_instance;
        }

        // Create new Instance synchroneously
        $instance = $this->createInstance($it, false);

        // Update Instance status
//        $this->setInstanceStatus($instance->getId(), "Running");
       
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
	$this->entityManager->flush();

	// stop instance for the time being
//	$this->stopInstance($instance);

	return true;
    }

    public function startInstance(Instances $instance, bool $async = true) {
        $this->logger->debug(__METHOD__);

        if ($async) {
            $this->lxdOperationBus->dispatch(new LxcOperation(["command" => "startInstance",
                        "name" => $instance->getName()]));
        } else {
            $this->lxdService->startObject($instance->getName());
        }
    }

    public function restartInstance(Instances $instance, bool $async = true) {
        $this->logger->debug(__METHOD__);

        if ($async) {
            $this->lxdOperationBus->dispatch(new LxcOperation(["command" => "restartInstance",
                        "name" => $instance->getName()]));
        } else {
            $this->lxdService->stopObject($instance->getName());
        }
    }

    public function stopInstance(Instances $instance, bool $async = true) {
        $this->logger->debug(__METHOD__);

        if ($async) {
            $this->lxdOperationBus->dispatch(new LxcOperation(["command" => "stopInstance",
                        "name" => $instance->getName()]));
        } else {
            $this->lxdService->restartObject($instance->getName());
        }
    }

    public function deleteInstance(Instances $instance) {
        $this->logger->debug(__METHOD__);

        $this->lxdOperationBus->dispatch(new LxcOperation(["command" => "deleteInstance",
                    "name" => $instance->getName()]));
    }
    
    public function deleteAllInstances() {
        $this->logger->debug(__METHOD__);

        $this->lxdOperationBus->dispatch(new LxcOperation(["command" => "deleteAllInstances"]));
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
    
    /**
     * 
     * @param Sessions $session
     * @return void
     */
    public function start(Sessions $session): void {
        $this->logger->debug(__METHOD__);

        $this->setSessionStatus($session, "Started");

        $this->setSessionTimestamp($session, "started");

        // Start certain number of instances
        $start_envs = $this->params['app_start_envs'];
        for ($i = 0; $i < $start_envs; $i++) {

            $this->allocateEnvironment($session);
        }
    }

    /**
     * 
     * @param Sessions $session
     * @return void
     */
    public function finish(Sessions $session): void {
        $this->logger->debug(__METHOD__);

        // Skip all remaining envs
        foreach ($session->getEnvs()->getValues() as $se) {

            if ($se->getStatus() == "Deployed") {

//	      $this->sessionManager->releaseInstance($se->getInstance());
/*
                $this->setEnvironmentStatus($se, "Skipped");
                $this->setEnvironmentTimestamp($se, "skipped");
*/
            }
        }
        $this->setSessionTimestamp($session, "finished");

        $this->setSessionStatus($session, "Finished");
    }

    /**
     * 
     * @param Sessions $session
     * @param string $status_str
     * @return bool
     */
    public function setSessionStatus(Sessions $session, string $status_str): bool {
        $this->logger->debug(__METHOD__);

        $status = $this->sessionStatusesRepository->findOneByStatus($status_str);

        if ($status) {

            $this->logger->debug('Changing session status to: ' . $status);

            $session->setStatus($status);

            // Store item into the DB
//	  $this->entityManager->persist($session);
            $this->entityManager->flush();

            return true;
        } else {

            $this->logger->debug('No such session status: ' . $status_str);

            return false;
        }
    }

    /*

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
*/

    /**
     * 
     * @param Sessions $session
     * @param string $timestamp_str
     * @return bool
     */
    public function setSessionTimestamp(Sessions $session, string $timestamp_str): bool
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
/*

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
*/

    /**
     * 
     * @param Sessions $session
     * @return bool
     */
    public function allocateEnvironment(Sessions $session): bool
    {
        $this->logger->debug(__METHOD__);

	// TODO: check input parameters
	$task = $this->getNextTask($session);

	$this->logger->debug( "Selected task: " . $task);

//	$environment = $this->environmentRepository->findOneDeployed($task->getId());
	$environments = $this->environmentRepository->findAllDeployed($task->getId());
	$environment = $environments ? reset($environments) : null;
/*
        if (count($environments)) {
            $environment = $environments[0];
        }
*/
        // Environment has been found
	if($environment) {

//	  $environment->setSession($session);

          $session->allocateEnvironment($environment);
                  
	  // Store item into the DB
//	  $this->entityManager->persist($environment);
	  $this->entityManager->flush();

	  $this->logger->debug( "Allocated environment: " . $environment);

	// No env to allocate, create it
	} else {

	  $this->logger->debug( "No suitable envs found. Requesting new env creation.");


            $this->environmentActionBus->dispatch(new EnvironmentAction(["action" => "create",
                        "task_id" => $task->getId(), "session_id" => $session->getId()]));
	  return false;	
	}
	return true;	
    }
    
    /**
     * 
     * @param Sessions $session
     * @return bool
     */
    public function removeSession(Sessions $session): bool {

        if ($session->getEnvsCounter() > 0) {

            foreach ($session->getEnvs() as $env) {
                $session->releaseEnvironment($env);
            }
        }

        $this->sessionsRepository->remove($session, true);

        return true;
    }
/*
    public function createEnvironment(Tasks $task, Sessions $session = null, bool $async = true): ?Environments {
        $this->logger->debug(__METHOD__);

        $session_id = null;
        if ($session) {
            $session_id = $session->getId();
        }
        if ($async) {
            $this->sessionBus->dispatch(new SessionAction(["action" => "createEnvironment",
                        "task_id" => $task->getId(), "session_id" => $session_id]));
        } else {
            return $this->envService->createEnvironment($task->getId(), $session_id);
        }
        return null;
    }
*/
    /*    
    public function createEnvironment(Tasks $task, Sessions $session = null): ?Environments {
        $this->logger->debug(__METHOD__);

        // TODO: check input parameters
        // Get the suitable InstanceType for a task
        $instance_type = $this->getFirstInstanceType($task);

        if (!$instance_type) {
            $this->logger->debug('No suitable instance types are available for task: ' . $task);
            return null;
        }
        $this->logger->debug('First suitable instance type: ' . $instance_type);

        $env = new Environments;

        $env_status = $this->environmentStatusesRepository->findOneByStatus("Created");
        $env->setStatus($env_status);

        $env->setTask($task);
        $env->setSession($session);

        // Store item into the DB
        $this->entityManager->persist($env);
//            $this->entityManager->flush();

        $this->logger->debug('Environment `' . $env . '` was created.');

//	  $timestamp = new \DateTimeImmutable('NOW');
//	  $env->setHash(substr(md5($timestamp->format('Y-m-d H:i:s')),0,8));
//	  $name = $this->createInstance($instance_type);
        $instance = $this->bindInstance($instance_type);

        $env->setInstance($instance);

        // Store item into the DB
//	  $this->entityManager->persist($env);
        $this->entityManager->flush();

        $this->logger->debug('Instance `' . $instance->getName() . 
                '` has been bound to the newly created environment.');

        $this->setInstanceStatus($instance->getId(), "Running");

//	  $this->setEnvironmentStatus($env, "Created");

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

//	  $this->setEnvironmentStatus($env, "Verified");

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
	  $result = $this->awxService->runJobTemplate($env->getTask()->getSolve(), $body);

	  $this->logger->debug('Status: ' . $result->status);

//	  $this->setEnvironmentStatus($env, "Solved");

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
          $this->awxService->runJobTemplate(55, $body);

	  // Deploy actual environment
	  $result = $this->awxService->runJobTemplate($env->getTask()->getDeploy(), $body);

	  $env->setDeployment($result->id);
	  $this->entityManager->flush();

	  $this->logger->debug('Status: ' . $result->status);

//	  $this->setEnvironmentStatus($env, "Deployed");

	  return true;

	} else {

	  $this->logger->debug('Deploy job template with id `' . $task_id . '` was NOT found.');
	}

	return false;
    }

*/

    /**
     * 
     * @return Tasks
     */
    public function getRandomTask(): Tasks
    {
        $this->logger->debug(__METHOD__);

        $tasks = $this->taskRepository->findAll();

        return $tasks[rand(0,count($tasks)-1)];
    }


    /**
     * 
     * @param Sessions $session
     * @return Tasks
     */
    public function getNextTask( Sessions $session): Tasks
    {
        $this->logger->debug(__METHOD__);


	// TODO: Select a task specifically for a session

        return $this->getRandomTask();
    }
/*
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
*/
}

