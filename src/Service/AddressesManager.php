<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Instances;
use App\Entity\Addresses;
use App\Repository\InstancesRepository;
use App\Repository\AddressesRepository;
use App\Service\LxcManager;

class AddressesManager {

    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private InstancesRepository $instanceRepository;
    private AddressesRepository $addressesRepository;
    private LxcManager $lxcService;
    
    public function __construct(
            LoggerInterface $logger, EntityManagerInterface $em,
            LxcManager $lxcManager
    ) {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);
        $this->addressesRepository = $this->entityManager->getRepository(Addresses::class);
        $this->lxcService = $lxcManager;
    }

    /**
     * 
     * @param Addresses $address
     */
    public function removeAddress(Addresses $address): void {

        $this->logger->debug(__METHOD__);

        $instances = $this->instanceRepository->findBy(['status' => $address->getId()]);

        if ($instances) {
            foreach ($instances as $instance) {
                $this->lxcService->wipeInstance($instance->getName(), $force = true);
            }
        }

        $this->addressesRepository->remove($address, true);
    }
}
