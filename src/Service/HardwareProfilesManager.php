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
use App\Service\InstanceTypesManager;

class HardwareProfilesManager {

    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private InstanceTypesRepository $itRepository;
    private OperatingSystemsRepository $osRepository;
    private HardwareProfilesRepository $hpRepository;
    private InstanceTypesManager $instanceTypesManager;
  
    public function __construct(
            LoggerInterface $logger, EntityManagerInterface $em,
            InstanceTypesManager $instanceTypesManager
    ) {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
        $this->itRepository = $this->entityManager->getRepository(InstanceTypes::class);
        $this->osRepository = $this->entityManager->getRepository(OperatingSystems::class);
        $this->hpRepository = $this->entityManager->getRepository(HardwareProfiles::class);
        
        $this->instanceTypesManager = $instanceTypesManager;
    }

    /**
     * 
     * @param HardwareProfiles $hp
     */
    public function removeHardwareProfile(HardwareProfiles $hp): void {

        $this->logger->debug(__METHOD__);

        foreach ($hp->getInstanceTypes() as $it) {
            $this->instanceTypesManager->removeInstanceType($it);
        }

        $this->hpRepository->remove($hp, true);
    }

    /**
     * 
     * @param HardwareProfiles $hp
     * @return bool
     */
    public function addHardwareProfile(HardwareProfiles $hp): bool {

        $this->logger->debug(__METHOD__);

        if ($this->hpRepository->add($hp, true)) {

            // Record addition or modification was successful
            // Make sure corresponding instance type exists
            // If we are working with supported item

            if ($hp->isSupported()) {
                $this->addInstanceTypes($hp);
            }
            return true;
        }
        return false;
    }

    /**
     * 
     * @param HardwareProfiles $hp
     * @return void
     */
    private function addInstanceTypes(HardwareProfiles $hp): void {
        
        $this->logger->debug(__METHOD__);
        
        // Get the supported OS list
        $oses = $this->osRepository->findBySupported(1);

        foreach ($oses as &$os) {

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
     * @param HardwareProfiles $hp
     * @return void
     */
    private function removeInstanceTypes(HardwareProfiles $hp): void {
        
        $this->logger->debug(__METHOD__);
        
        $instance_types = $this->itRepository->findBy(['hw_profile' => $hp->getId()]);

        foreach ($instance_types as &$it) {
            $this->instanceTypesManager->removeInstanceType($it);
        }
    }

    /**
     * 
     * @param HardwareProfiles $hp
     * @return void
     */
    public function editHardwareProfile(HardwareProfiles $hp): void {

        $this->logger->debug(__METHOD__);
        
        if ($this->addHardwareProfile($hp)) {

            // Record modification was successfull
            // If HP became unsupported delete corresponding instance types
            if (!$hp->isSupported()) {
                $this->removeInstanceTypes($hp);
            }
        }
    }
}
