<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Instances;
use App\Entity\InstanceStatuses;
use App\Repository\InstancesRepository;
use App\Repository\InstanceStatusesRepository;
use App\Service\LxcManager;

class InstanceStatusesManager {

    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private InstancesRepository $instanceRepository;
    private InstanceStatusesRepository $instanceStatusesRepository;
    private LxcManager $lxcService;
    
    public function __construct(
            LoggerInterface $logger, EntityManagerInterface $em,
            LxcManager $lxcManager
    ) {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);
        $this->instanceStatusesRepository = $this->entityManager->getRepository(InstanceStatuses::class);
        $this->lxcService = $lxcManager;
    }

    /**
     * 
     * @param InstanceStatuses $instanceStatus
     */
    public function removeInstanceStatus(InstanceStatuses $instanceStatus): void {

        $this->logger->debug(__METHOD__);

        $instances = $this->instanceRepository->findBy(['status' => $instanceStatus->getId()]);

        if ($instances) {
            foreach ($instances as $instance) {
                $this->lxcService->wipeInstance($instance->getName(), $force = true);
            }
        }

        $this->instanceStatusesRepository->remove($instanceStatus, true);
    }
}
