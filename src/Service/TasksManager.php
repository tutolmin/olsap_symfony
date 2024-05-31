<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Environments;
use App\Entity\Tasks;
use App\Repository\EnvironmentsRepository;
use App\Repository\TasksRepository;
use App\Service\EnvironmentManager;

class TasksManager {

    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private EnvironmentsRepository $environmentRepository;
    private TasksRepository $environmentStatusesRepository;
    private EnvironmentManager $environmentManager;
    
    public function __construct(
            LoggerInterface $logger, EntityManagerInterface $em,
            EnvironmentManager $environmentManager
    ) {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
        $this->environmentRepository = $this->entityManager->getRepository(Environments::class);
        $this->environmentStatusesRepository = $this->entityManager->getRepository(Tasks::class);
        $this->environmentManager = $environmentManager;
    }

    /**
     * 
     * @param Tasks $task
     */
    public function removeTask(Tasks $task): void {

        $this->logger->debug(__METHOD__);

        $environments = $this->environmentRepository->findBy(['task' => $task->getId()]);

        if ($environments) {
            foreach ($environments as $environment) {
                $this->environmentManager->deleteEnvironment($environment);
            }
        }

        $this->environmentStatusesRepository->remove($task, true);
    }
}
