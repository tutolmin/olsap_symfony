<?php

// src/Service/SessionManager.php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Sessions;
use App\Entity\Tasks;
use App\Entity\InstanceTypes;

class SessionManager
{
    private $logger;

    private $entityManager;
    private $taskRepository;

    public function __construct( LoggerInterface $logger, EntityManagerInterface $em)
    {
        $this->logger = $logger;
        $this->entityManager = $em;

        // get the task repository
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
    }

    public function getNextTask( Sessions $session): Tasks
    {
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

