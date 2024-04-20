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

class OperatingSystemsManager
{    
    private LoggerInterface $logger;

    private EntityManagerInterface $entityManager;
    private InstanceTypesRepository $itRepository;
    private OperatingSystemsRepository $osRepository;
   /**
     *
     * @var HardwareProfilesRepository
     */
    private $hpRepository;


    public function __construct( 
            LoggerInterface $logger, EntityManagerInterface $em
            )
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;

        $this->itRepository = $this->entityManager->getRepository( InstanceTypes::class);
        $this->osRepository = $this->entityManager->getRepository( OperatingSystems::class);
        $this->hpRepository = $this->entityManager->getRepository( HardwareProfiles::class);
    }

    public function addOs( OperatingSystems $os): bool
    {
	if( $this->osRepository->add($os, true)){


        // Record additioin or modification was successful
        // Make sure corresponding instance type exists
        // If we are working with supported item

	if($os->isSupported())
	{
		$this->addInstanceTypes($os);	
	}

return true;

	}

return false;
    }


    public function addInstanceTypes( OperatingSystems $os): void
{
	        // Get the supported HP list
        $hw_profiles = $this->hpRepository->findBySupported(1);

            foreach ($hw_profiles as &$hp) {

            $it = $this->itRepository->findBy(['os' => $os->getId(), 'hw_profile' => $hp->getId()]);

	if(!$it){
        $instanceType = new InstanceTypes();
	$instanceType->setOs($os);
	$instanceType->setHwProfile($hp);
            $this->itRepository->add($instanceType, true);

}
		}

} 

    public function removeInstanceTypes( OperatingSystems $os): void
{
            $instance_types = $this->itRepository->findBy(['os' => $os->getId()]);

            foreach ($instance_types as &$it) {
            $this->itRepository->remove($it, true);

}

} 

    public function editOs( OperatingSystems $os): void
    {

	     if($this->addOs($os))
{

	// Record modification was successfull
	// If OS became unsupported delete corresponding instance types
	if(!$os->isSupported())
        {
                $this->removeInstanceTypes($os);
        }
}
    }

}

