<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\InstanceTypesRepository;
//use App\Repository\HardwareProfilesRepository;
//use App\Entity\HardwareProfiles;
use App\Entity\OperatingSystems;
use App\Entity\InstanceTypes;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OperatingSystemsRepository;
use Psr\Log\LoggerInterface; 

class OperatingSystemsTest extends KernelTestCase
{
    private LoggerInterface $logger;
    
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    /**
     * 
     * @var HardwareProfilesRepository
     */
//    private $hpRepository;

    /**
     * 
     * @var OperatingSystemsRepository
     */
    private $osRepository;

    /**
     * 
     * @var InstanceTypesRepository
     */
    private $itRepository;

    protected function setUp(): void {
        self::bootKernel();
    
        $this->logger = static::getContainer()->get('Psr\Log\LoggerInterface');

        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');

        // get the HW profile repository
//        $this->hpRepository = $this->entityManager->getRepository( HardwareProfiles::class);

        // get the OS repository
        $this->osRepository = $this->entityManager->getRepository(OperatingSystems::class);

        // get the InstanceTypes repository
        $this->itRepository = $this->entityManager->getRepository(InstanceTypes::class);
    }

    public function testOperatingSystemsListIsNotEmpty(): void {
//        $this->logger->debug(__METHOD__);

        $this->assertNotEmpty($this->osRepository->findAll());
    }
    
    /**
     * 
     * @return array<OperatingSystems>
     */
    public function testSupportedOperatingSystemsListIsNotEmpty() {
//        $this->logger->debug(__METHOD__);

        $oses = $this->osRepository->findBySupported(true);

        $this->assertNotEmpty($oses);
        
        return $oses;
    }
    
    /**
     * 
     * @param array<OperatingSystems> $oses
     * @depends testSupportedOperatingSystemsListIsNotEmpty
     * @return void
     */
    public function testEachSupportedOsHasCorrespondingInstanceTypes( array $oses): void {
        $this->logger->debug(__METHOD__);

//        $this->logger->debug(sprintf('Supported OSes count: %s', count($oses)));

        // Iterate through all the OSes
        foreach ($oses as &$os) {

//            $this->logger->debug(sprintf('OS: %s %s', $os->getBreed(), $os->getRelease()));
            
            // Try to find existing Instance type
            $it = $this->itRepository->findBy(['os' => $os->getId()]);

            $this->assertEmpty($it);
        }
    }
}
