<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Addresses;
use App\Repository\AddressesRepository;
use App\Service\LxcManager;

class AddressesManager {

    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private AddressesRepository $addressesRepository;
    private LxcManager $lxcService;
    
    public function __construct(
            LoggerInterface $logger, EntityManagerInterface $em,
            LxcManager $lxcManager
    ) {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
        $this->addressesRepository = $this->entityManager->getRepository(Addresses::class);
        $this->lxcService = $lxcManager;
    }

    /**
     * 
     * @param Addresses $address
     */
    public function removeAddress(Addresses $address): void {

        $this->logger->debug(__METHOD__);

        $instance = $address->getInstance();

        if ($instance) {
            $this->lxcService->wipeInstance($instance->getName(), $force = true);
        }

        $this->addressesRepository->remove($address, true);
    }
}
