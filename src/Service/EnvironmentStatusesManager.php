<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Environments;
use App\Entity\EnvironmentStatuses;
use App\Repository\EnvironmentsRepository;
use App\Repository\EnvironmentStatusesRepository;
use App\Service\EnvironmentManager;

class EnvironmentStatusesManager {

    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private EnvironmentsRepository $environmentRepository;
    private EnvironmentStatusesRepository $environmentStatusesRepository;
    private EnvironmentManager $environmentManager;
    
    public function __construct(
            LoggerInterface $logger, EntityManagerInterface $em,
            EnvironmentManager $environmentManager
    ) {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
        $this->environmentRepository = $this->entityManager->getRepository(Environments::class);
        $this->environmentStatusesRepository = $this->entityManager->getRepository(EnvironmentStatuses::class);
        $this->environmentManager = $environmentManager;
    }

    /**
     * 
     * @param EnvironmentStatuses $environmentStatus
     */
    public function removeEnvironmentStatus(EnvironmentStatuses $environmentStatus): void {

        $this->logger->debug(__METHOD__);

        $environments = $this->environmentRepository->findBy(['status' => $environmentStatus->getId()]);

        if ($environments) {
            foreach ($environments as $environment) {
                $this->environmentManager->deleteEnvironment($environment);
            }
        }

        $this->environmentStatusesRepository->remove($environmentStatus, true);
    }
}
