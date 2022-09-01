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

class SessionManager
{
    private $logger;

    private $entityManager;
    private $taskRepository;
    private $addressRepository;
    private $isRepository;
    private $esRepository;

    private $lxd;

    public function __construct( LoggerInterface $logger, EntityManagerInterface $em, LxcManager $lxd)
    {
        $this->logger = $logger;
        $this->entityManager = $em;
	$this->lxd = $lxd;

        // get the repositories
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
        $this->addressRepository = $this->entityManager->getRepository( Addresses::class);
        $this->isRepository = $this->entityManager->getRepository( InstanceStatuses::class);
        $this->esRepository = $this->entityManager->getRepository( EnvironmentStatuses::class);
    }

    public function createInstance(InstanceTypes $it): Instances
    {
	// TODO: check input parameters

	// Find an address item which is NOT linked to any instance
	$address = $this->addressRepository->findOneByInstance(null);
	
	// TODO: Handle situation when no such addresses found

	$this->logger->debug( "Selected address: " . $address->getIp() . ", MAC: " . $address->getMac());

	$name = $this->lxd->createInstance($it->getOs()->getAlias(), $it->getHwProfile()->getNAme(), $address->getMac());

	$this->logger->debug('Instance `' . $name . '` was created.');

	$instance = new Instances;
	$instance->setName($name);
	$instance_status = $this->isRepository->findOneByStatus("Bound");
	$instance->setStatus($instance_status);
	$instance->setInstanceType($it);
	$now = new \DateTimeImmutable('NOW');
	$instance->setCreatedAt($now);

	$address->setInstance($instance);

	// Store item into the DB
	$this->entityManager->persist($instance);
	$this->entityManager->flush();

	return $instance;
    }

    public function createEnvironment(Tasks $task, Sessions $session = null): ?Environments
    {
	// TODO: check input parameters

	// Get the suitable InstanceType for a task
	$instance_type = $this->getFirstInstanceType($task);

	if($instance_type) { 

	  $this->logger->debug('First suitable instance type: '.$instance_type);

	  $env = new Environments;
	  $env_status = $this->esRepository->findOneByStatus("Created");
	  $env->setStatus($env_status);
	  $env->setTask($task);
	  $env->setSession($session);

	  $name = $this->createInstance($instance_type);

	  $env->setInstance($name);

	  // Store item into the DB
	  $this->entityManager->persist($env);
	  $this->entityManager->flush();

	  $this->logger->debug('Environment `' . $env . '` was created.');

	  return $env;

	} else

	  $this->logger->debug('No suitable instance types are available for task: '.$task);

	return null;
    }

    public function getNextTask( Sessions $session): Tasks
    {

	// TODO: Select a task specifically for a session

        $tasks = $this->taskRepository->findAll();

        return $tasks[rand(0,count($tasks)-1)];
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

