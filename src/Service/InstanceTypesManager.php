<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Instances;
use App\Entity\InstanceTypes;
use App\Repository\InstancesRepository;
use App\Repository\InstanceTypesRepository;
use App\Service\LxcManager;

class InstanceTypesManager {

    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private InstancesRepository $instanceRepository;
    private InstanceTypesRepository $instanceTypesRepository;
    private LxcManager $lxcService;
    
    public function __construct(
            LoggerInterface $logger, EntityManagerInterface $em,
            LxcManager $lxcManager
    ) {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);
        $this->instanceTypesRepository = $this->entityManager->getRepository(InstanceTypes::class);
        $this->lxcService = $lxcManager;
    }

    /**
     * 
     * @param InstanceTypes $instanceType
     */
    public function removeInstanceType(InstanceTypes $instanceType): void {

        $this->logger->debug(__METHOD__);

        $instances = $this->instanceRepository->findBy(['instance_type' => $instanceType->getId()]);

        if ($instances) {
            foreach ($instances as $instance) {
                $this->lxcService->wipeInstance($instance->getName(), $force = true);
            }
        }

        $this->instanceTypesRepository->remove($instanceType, true);
    }
}
