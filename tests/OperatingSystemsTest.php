<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\InstanceTypesRepository;
use App\Repository\HardwareProfilesRepository;
use App\Repository\OperatingSystemsRepository;
use App\Repository\BreedsRepository;
use App\Entity\HardwareProfiles;
use App\Entity\OperatingSystems;
use App\Entity\InstanceTypes;
use App\Entity\Breeds;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\OperatingSystemsManager;

class OperatingSystemsTest extends KernelTestCase
{    
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    /**
     * 
     * @var array<string>
     */
    private $dummy = array('name'=>'Dummy');
    
    /**
     * 
     * @var HardwareProfilesRepository
     */
    private $hpRepository;

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

    /**
     * 
     * @var BreedsRepository
     */
    private $breedsRepository;

    /**
     * 
     * @var OperatingSystemsManager
     */
    private $osManager;
    
    /**
     * 
     * @return array<HardwareProfiles>
     */
    private function getSupportedHardwareProfiles(): array {
        // Get the supported HP list
	$hw_profiles = $this->hpRepository->findBySupported(true);
        $this->assertNotEmpty($hw_profiles); 
        return $hw_profiles;       
    }
  
    protected function setUp(): void {
        self::bootKernel();

//        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->hpRepository = $this->entityManager->getRepository( HardwareProfiles::class);
        $this->itRepository = $this->entityManager->getRepository(InstanceTypes::class);
        $this->osRepository = $this->entityManager->getRepository(OperatingSystems::class);
        $this->breedsRepository = $this->entityManager->getRepository(Breeds::class);
        $this->osManager = static::getContainer()->get(OperatingSystemsManager::class);        
    }

    public function testOperatingSystemsListIsNotEmpty(): void {

        $this->assertNotEmpty($this->osRepository->findAll());
    }
    
    /**
     * @depends testOperatingSystemsListIsNotEmpty
     * @return array<OperatingSystems>
     */
    public function testSupportedOperatingSystemsListIsNotEmpty() {

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
    public function testEachSupportedOsHasCorrespondingInstanceTypesForAllSupportedHardwareProfiles( array $oses): void {

	$hw_profiles = $this->getSupportedHardwareProfiles();

        // Iterate through all the OSes
        foreach ($oses as &$os) {

            foreach ($hw_profiles as &$hp) {

                // Try to find existing Instance type
                $this->assertNotEmpty($this->itRepository->findBy(
                                ['os' => $os->getId(), 'hw_profile' => $hp->getId()]));
            }
        }
    }
    
    public function testCanNotAddOperatingSystemWithoutReleaseOrBreed(): void {
        
        $this->assertFalse($this->osManager->addOperatingSystem(new OperatingSystems()));
    }

    /**
     * @depends testOperatingSystemsListIsNotEmpty
     * @return void
     */
    public function testCanNotAddDuplicateOperatingSystem(): void {
                
        $os = $this->osRepository->findOneBy(array());
        $this->assertNotNull($os);
      
        $new_os = new OperatingSystems();
        $new_os->setSupported($os->isSupported());
        $new_os->setBreed($os->getBreed());
        $new_os->setRelease($os->getRelease());
        $this->assertFalse($this->osManager->addOperatingSystem($new_os));
    }    
    
    /**
     * 
     * @return OperatingSystems
     */ 
    public function testCanAddDummyOperatingSystem(): OperatingSystems {

        $breed = $this->breedsRepository->findOneBy(array());
        $this->assertNotNull($breed);

        $os = new OperatingSystems();
        $os->setSupported(false);
        $os->setBreed($breed);
        $os->setRelease($this->dummy['name']);
        $os->setAlias($this->dummy['name']);
        $this->assertTrue($this->osManager->addOperatingSystem($os));
        
        return $os;
    }

    public function testCanAddDummySupportedOperatingSystem(): OperatingSystems {
        
        $breed = $this->breedsRepository->findOneBy(array());
        $this->assertNotNull($breed);
        
        // Adding dummy os
        $os = new OperatingSystems();
        $os->setRelease($this->dummy['name']);
        $os->setSupported(true);
        $os->setBreed($breed);
        $this->assertTrue($this->osManager->addOperatingSystem($os));

        $hw_profiles = $this->getSupportedHardwareProfiles();

        // Iterate through all the HPs
        foreach ($hw_profiles as &$hp) {

            // Try to find existing Instance type
            $this->assertNotEmpty($this->itRepository->findBy(
                            ['os' => $os->getId(), 'hw_profile' => $hp->getId()]));
        }
        return $os;
    }
            
    /**
     * @depends testSupportedOperatingSystemsListIsNotEmpty
     * @param array<OperatingSystems> $oses
     * @return void
     */    
    public function testCanRemoveAllOperatingSystems($oses): void {

        foreach ($oses as $s) {

            $item = $this->osRepository->findOneById($s);
            $this->assertNotNull($item);
            $id = $item->getId();

            $this->osManager->removeOperatingSystem($item);

            $removed_item = $this->osRepository->findOneById($id);
            $this->assertNull($removed_item);
        }

        $this->assertEmpty($this->itRepository->findAll());
    }    

    /**
     * @depends testOperatingSystemsListIsNotEmpty
     * @return void
     */    
    public function testCanMakeOperatingSystemSupported(): void {

        $os = $this->osRepository->findOneBySupported(false);
        $this->assertNotNull($os);
        
        $os->setSupported(true);
        
        $this->osManager->editOperatingSystem($os);

	$hw_profiles = $this->getSupportedHardwareProfiles();
        
        // Iterate through all the HPs
        foreach ($hw_profiles as &$hp) {

            // Try to find existing Instance type
            $this->assertNotEmpty($this->itRepository->findBy(
                            ['os' => $os->getId(), 'hw_profile' => $hp->getId()]));
        }
    }
    
    /**
     * @depends testSupportedOperatingSystemsListIsNotEmpty
     * @param array<OperatingSystems> $oses
     * @return void
     */    
    public function testCanMakeOperatingSystemUnsupported( $oses): void {

        $item = $this->osRepository->findOneById($oses[0]);
        $this->assertNotNull($item);

        $item->setSupported(false);
        
        $this->osManager->editOperatingSystem($item);

	$hw_profiles = $this->getSupportedHardwareProfiles();
        
        // Iterate through all the HPs
        foreach ($hw_profiles as &$hp) {

            // Try to find existing Instance type
            $this->assertEmpty($this->itRepository->findBy(
                            ['os' => $item->getId(), 'hw_profile' => $hp->getId()]));
        }
    }    
}
