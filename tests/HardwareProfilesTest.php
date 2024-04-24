<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\InstanceTypesRepository;
use App\Repository\HardwareProfilesRepository;
use App\Repository\OperatingSystemsRepository;
use App\Entity\HardwareProfiles;
use App\Entity\InstanceTypes;
use App\Entity\OperatingSystems;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\HardwareProfilesManager;

class HardwareProfilesTest extends KernelTestCase {

    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * 
     * @var array<string>
     */
    private $dummy = array('name' => 'Dummy');

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

    /**
     * 
     * @var OperatingSystemsRepository
     */
    private $osRepository;

    /**
     * 
     * @var HardwareProfilesManager
     */
    private $hpManager;

    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
//        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
        $this->hpRepository = $this->entityManager->getRepository(HardwareProfiles::class);
        $this->itRepository = $this->entityManager->getRepository(InstanceTypes::class);
        $this->osRepository = $this->entityManager->getRepository(OperatingSystems::class);
        $this->hpManager = static::getContainer()->get(HardwareProfilesManager::class);
    }

    public function testHardwareProfilesListIsNotEmpty(): void {

        $this->assertNotEmpty($this->hpRepository->findAll());
    }

    /**
     * @depends testHardwareProfilesListIsNotEmpty
     * @return array<HardwareProfiles>
     */
    public function testContainerHardwareProfilesListIsNotEmpty() {

        $hp = $this->hpRepository->findByType(false);

        $this->assertNotEmpty($hp);

        return $hp;
    }

    /**
     * @depends testHardwareProfilesListIsNotEmpty
     * @return array<HardwareProfiles>
     */
    public function testVmHardwareProfilesListIsNotEmpty() {

        $hp = $this->hpRepository->findByType(true);

        $this->assertNotEmpty($hp);

        return $hp;
    }

    /**
     * @depends testHardwareProfilesListIsNotEmpty
     * @return array<HardwareProfiles>
     */
    public function testSupportedHardwareProfilesListIsNotEmpty() {

        $hp = $this->hpRepository->findBySupported(true);

        $this->assertNotEmpty($hp);

        return $hp;
    }
    
    /**
     * 
     * @return array<OperatingSystems>
     */
    private function getSupportedOperatingSystems(): array {
        // Get the supported HP list
	$operating_systems = $this->osRepository->findBySupported(true);
        $this->assertNotEmpty($operating_systems); 
        return $operating_systems;       
    }
    
    /**
     * @param array<HardwareProfiles> $hp
     * @depends testSupportedHardwareProfilesListIsNotEmpty
     * @return void
     */
    public function testEachSupportedHardwareProfileHasCorrespondingInstanceTypesForAllSupportedOses(array $hp): void {

        // Get the supported OS list
        $oses = $this->osRepository->findBySupported(true);
        $this->assertNotEmpty($oses);

        // Iterate through all the HPs
        foreach ($hp as &$hw_profile) {

            foreach ($oses as &$os) {

                // Try to find existing Instance type
                $this->assertNotEmpty($this->itRepository->findBy(
                                ['os' => $os->getId(), 'hw_profile' => $hw_profile->getId()]));
            }
        }
    }

    public function testCanNotAddHardwareProfileWithoutNameOrType(): void {

        $this->assertFalse($this->hpManager->addHardwareProfile(new HardwareProfiles()));
    }

    /**
     * @depends testHardwareProfilesListIsNotEmpty
     * @return void
     */
    public function testCanNotAddDuplicateHardwareProfile(): void {

        $hp = $this->hpRepository->findOneBy(array());
        $this->assertNotNull($hp);

        $new_hp = new HardwareProfiles();
        $new_hp->setName($hp->getName());
        $new_hp->setSupported($hp->isSupported());
        $new_hp->setType($hp->isType());
        $this->assertFalse($this->hpManager->addHardwareProfile($new_hp));
    }
    
    public function testCanAddDummyHardwareProfile(): void {

        $hp = new HardwareProfiles();
        $hp->setName($this->dummy['name']);
        $hp->setSupported(false);
        $hp->setType(false);
        $this->assertTrue($this->hpManager->addHardwareProfile($hp));
    }

    public function testCanAddDummySupportedHardwareProfile(): void {

        // Adding dummy supported profile
        $hw_profile = new HardwareProfiles();
        $hw_profile->setName($this->dummy['name']);
        $hw_profile->setSupported(true);
        $hw_profile->setType(false);
        $this->assertTrue($this->hpManager->addHardwareProfile($hw_profile));

        // Get the supported OS list
        $oses = $this->osRepository->findBySupported(true);
        $this->assertNotEmpty($oses);

        // Iterate through all the OSes
        foreach ($oses as &$os) {

            // Try to find existing Instance type
            $this->assertNotEmpty($this->itRepository->findBy(
                            ['os' => $os->getId(), 'hw_profile' => $hw_profile->getId()]));
        }
    }

    /**
     * @depends testHardwareProfilesListIsNotEmpty
     * @return void
     */    
    public function testCanRemoveUnsupportedHardwareProfile(): void {
        $this->markTestSkipped(
                'Cascade delete is way to complicated with all the references',
            );
        /*
        $hp = $this->hpRepository->findOneBySupported(false);
        $this->assertNotNull($hp);

        $this->assertTrue($this->hpManager->removeHardwareProfile($hp));
         * 
         */
    }
            
    /**
     * @depends testSupportedHardwareProfilesListIsNotEmpty
     * @param array<HardwareProfiles> $hw_profiles 
     * @return void
     */    
    public function testCanNotRemoveSupportedHardwareProfile(array $hw_profiles): void {
        
        $this->assertFalse($this->hpManager->removeHardwareProfile($hw_profiles[0]));
    }
            
    /**
     * @depends testSupportedHardwareProfilesListIsNotEmpty
     * @param array<HardwareProfiles> $hw_profiles 
     * @return void
     */    
    public function testCanRemoveSupportedHardwareProfileWithCascadeFlag(
            array $hw_profiles): void {
        $this->markTestSkipped(
                'Cascade delete is way to complicated with all the references',
            );
        /*
        $hw_profile = $this->hpRepository->findOneBySupported(true);
        $this->assertNotNull($hw_profile);
        
        $hp_id = $hw_profile->getId();

        $this->assertTrue($this->hpManager->removeHardwareProfile($hw_profile, true));

	$oses = $this->getSupportedOperatingSystems();
        
        // Iterate through all the OSes
        foreach ($oses as &$os) {

            // Try to find existing Instance type
            $this->assertEmpty($this->itRepository->findBy(
                            ['os' => $os->getId(), 'hw_profile' => $hp_id]));
        }
         * 
         */
    }

    /**
     * @depends testHardwareProfilesListIsNotEmpty
     * @return void
     */    
    public function testCanMakeHardwareProfileSupported(): void {

        $hw_profile = $this->hpRepository->findOneBySupported(false);
        $this->assertNotNull($hw_profile);
        
        $hw_profile->setSupported(true);
        
        $this->hpManager->editHardwareProfile($hw_profile);

	$oses = $this->getSupportedOperatingSystems();
        
        // Iterate through all the OSes
        foreach ($oses as &$os) {

            // Try to find existing Instance type
            $this->assertNotEmpty($this->itRepository->findBy(
                            ['os' => $os->getId(), 'hw_profile' => $hw_profile->getId()]));
        }
    }
    
    /**
     * @depends testSupportedHardwareProfilesListIsNotEmpty
     * @param array<HardwareProfiles> $hw_profiles 
     * @return void
     */    
    public function testCanMakeHardwareProfileUnsupported(array $hw_profiles): void {

        $hp = $this->hpRepository->findOneById($hw_profiles[0]->getId());
        $this->assertNotNull($hp);
        
        $hp->setSupported(false);
        
        $this->hpManager->editHardwareProfile($hp);

	$oses = $this->getSupportedOperatingSystems();
        
        // Iterate through all the OSes
        foreach ($oses as &$os) {

            // Try to find existing Instance type
            $this->assertEmpty($this->itRepository->findBy(
                            ['os' => $os->getId(), 'hw_profile' => $hp->getId()]));
        }
    }    
}
