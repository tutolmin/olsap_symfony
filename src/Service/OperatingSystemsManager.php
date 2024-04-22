<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\InstanceTypes;
use App\Entity\OperatingSystems;
use App\Entity\HardwareProfiles;
use App\Repository\OperatingSystemsRepository;
use App\Repository\InstanceTypesRepository;
use App\Repository\HardwareProfilesRepository;

class OperatingSystemsManager {

    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private InstanceTypesRepository $itRepository;
    private OperatingSystemsRepository $osRepository;
    private HardwareProfilesRepository $hpRepository;

    public function __construct(
            LoggerInterface $logger, EntityManagerInterface $em
    ) {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
        $this->itRepository = $this->entityManager->getRepository(InstanceTypes::class);
        $this->osRepository = $this->entityManager->getRepository(OperatingSystems::class);
        $this->hpRepository = $this->entityManager->getRepository(HardwareProfiles::class);
    }

    /**
     * 
     * @param OperatingSystems $os
     * @return bool
     */
    public function removeOperatingSystem(OperatingSystems $os): bool {
        if ($this->osRepository->remove($os, true)) {
            return true;
        }
        return false;
    }

    /**
     * 
     * @param OperatingSystems $os
     * @return bool
     */
    public function addOperatingSystem(OperatingSystems $os): bool {
        if ($this->osRepository->add($os, true)) {


            // Record additioin or modification was successful
            // Make sure corresponding instance type exists
            // If we are working with supported item

            if ($os->isSupported()) {
                $this->addInstanceTypes($os);
            }

            return true;
        }

        return false;
    }

    /**
     * 
     * @param OperatingSystems $os
     * @return void
     */
    private function addInstanceTypes(OperatingSystems $os): void {
        // Get the supported HP list
        $hw_profiles = $this->hpRepository->findBySupported(1);

        foreach ($hw_profiles as &$hp) {

            $it = $this->itRepository->findBy(['os' => $os->getId(), 'hw_profile' => $hp->getId()]);

            if (!$it) {
                $instanceType = new InstanceTypes();
                $instanceType->setOs($os);
                $instanceType->setHwProfile($hp);
                $this->itRepository->add($instanceType, true);
            }
        }
    }

    /**
     * 
     * @param OperatingSystems $os
     * @return void
     */
    private function removeInstanceTypes(OperatingSystems $os): void {
        $instance_types = $this->itRepository->findBy(['os' => $os->getId()]);

        foreach ($instance_types as &$it) {
            $this->itRepository->remove($it, true);
        }
    }

    /**
     * 
     * @param OperatingSystems $os
     * @return void
     */
    public function editOperatingSystem(OperatingSystems $os): void {

        if ($this->addOperatingSystem($os)) {

            // Record modification was successfull
            // If OS became unsupported delete corresponding instance types
            if (!$os->isSupported()) {
                $this->removeInstanceTypes($os);
            }
        }
    }
}
