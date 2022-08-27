<?php

// src/Service/AwxManager.php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

//use App\Entity\Tasks;
//use App\Entity\InstanceTypes;

class AwxManager
{
    private $logger;

    private $entityManager;
//    private $taskRepository;

    public function __construct( LoggerInterface $logger, EntityManagerInterface $em)
    {
        $this->logger = $logger;
        $this->entityManager = $em;

        // get the task repository
//        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
    }
/*
    public function getNextTask( Awx $session): Tasks
    {
        $tasks = $this->taskRepository->findAll();

        return $tasks[rand(0,count($tasks)-1)];
    }
*/

}

