<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\InstanceTypesRepository;
use App\Repository\HardwareProfilesRepository;
use App\Entity\HardwareProfiles;
use App\Entity\InstanceTypes;
use Doctrine\ORM\EntityManagerInterface;
//use Psr\Log\LoggerInterface; 

class HardwareProfilesTest extends KernelTestCase
{
//    private LoggerInterface $logger;
    
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    /**
     * 
     * @var HardwareProfilesRepository
     */
    private $hpRepository;

    /**
     * 
     * @var InstanceTypesRepository
     */
    private $itRepository;

    protected function setUp(): void {
        self::bootKernel();
    
//        $this->logger = static::getContainer()->get('Psr\Log\LoggerInterface');

        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');

        // get the HW profile repository
        $this->hpRepository = $this->entityManager->getRepository( HardwareProfiles::class);

        // get the InstanceTypes repository
        $this->itRepository = $this->entityManager->getRepository(InstanceTypes::class);
    }

    public function testHardwareProfilesListIsNotEmpty(): void {
//        $this->logger->debug(__METHOD__);

        $this->assertNotEmpty($this->hpRepository->findAll());
    }
    
    /**
     * 
     * @return array<HardwareProfiles>
     */
    public function testContainerHardwareProfilesListIsNotEmpty() {
//        $this->logger->debug(__METHOD__);

        $hp = $this->hpRepository->findByType(false);

        $this->assertNotEmpty($hp);
        
        return $hp;
    }
      
    /**
     * 
     * @return array<HardwareProfiles>
     */
    public function testVmHardwareProfilesListIsNotEmpty() {
//        $this->logger->debug(__METHOD__);

        $hp = $this->hpRepository->findByType(false);

        $this->assertNotEmpty($hp);
        
        return $hp;
    }
    
    /**
     * 
     * @return array<HardwareProfiles>
     */
    public function testSupportedHardwareProfilesListIsNotEmpty() {
//        $this->logger->debug(__METHOD__);

        $hp = $this->hpRepository->findBySupported(true);

        $this->assertNotEmpty($hp);
        
        return $hp;
    }
    
    /**
     * 
     * @param array<HardwareProfiles> $hp
     * @depends testSupportedHardwareProfilesListIsNotEmpty
     * @return void
     */
    public function testEachSupportedHardwareProfileHasCorrespondingInstanceTypes( array $hp): void {
//        $this->logger->debug(__METHOD__);
       
//        $this->logger->debug(sprintf('Supported hardware profiles count: %s', count($hp)));

        // Iterate through all the OSes
        foreach ($hp as &$hw_profile) {
/*
            $this->logger->debug(sprintf('HP: %s %s', 
                    $hw_profile->getName(), $hw_profile->getDescription()));
*/            
            // Try to find existing Instance type
            $it = $this->itRepository->findBy(['hw_profile' => $hw_profile->getId()]);

            $this->assertNotEmpty($it);
        }
    }
}
