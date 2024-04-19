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
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

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
    
    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
        $this->hpRepository = $this->entityManager->getRepository( HardwareProfiles::class);
        $this->itRepository = $this->entityManager->getRepository(InstanceTypes::class);
        $this->osRepository = $this->entityManager->getRepository(OperatingSystems::class);
        $this->breedsRepository = $this->entityManager->getRepository(Breeds::class);
    }

    public function testOperatingSystemsListIsNotEmpty(): void {
//        $this->logger->debug(__METHOD__);

        $this->assertNotEmpty($this->osRepository->findAll());
    }
    
    /**
     * @depends testOperatingSystemsListIsNotEmpty
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
    public function testEachSupportedOsHasCorrespondingInstanceTypesForAllSupportedHardwareProfiles( array $oses): void {

        // Get the supported HP list
	$hw_profiles = $this->hpRepository->findBySupported(1);
        $this->assertNotEmpty($hw_profiles);

        // Iterate through all the OSes
        foreach ($oses as &$os) {

            foreach ($hw_profiles as &$hp) {

            // Try to find existing Instance type
            $it = $this->itRepository->findBy(['os' => $os->getId(), 'hw_profile' => $hp->getId()]);

            $this->assertNotEmpty($it);

            }
        }
    }
    
    public function testCanNotAddOperatingSystemWithoutNameOrBreed(): void {
        
        $this->expectException(NotNullConstraintViolationException::class);
        $this->osRepository->add(new OperatingSystems(), true);
    }
    
    /**
     * @return void
     */    
    public function testCanAddAndRemoveDummyOperatingSystem(): void {

        $breed = $this->breedsRepository->findOneBy(array());
        $this->assertNotNull($breed);

        $os = new OperatingSystems();
        $os->setSupported(false);
        $os->setBreed($breed);
        $os->setRelease($this->dummy['name']);
        $this->osRepository->add($os, true);
        $this->osRepository->remove($os, true);
    }

    /**
     * @depends testSupportedOperatingSystemsListIsNotEmpty
     * @return void
     */
    public function testCanAddAndRemoveDummySupportedOperatingSystem(): void {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'This test has not been implemented yet.',
        );
    }
    
    /**
     * @depends testOperatingSystemsListIsNotEmpty
     * @return void
     */
    public function testCanNotAddDuplicateOperatingSystem(): void {
        
        $this->expectException(UniqueConstraintViolationException::class);
        
        $os = $this->osRepository->findOneBy(array());
        $this->assertNotNull($os);
      
        $new_os = new OperatingSystems();
        $new_os->setSupported($os->isSupported());
        $new_os->setBreed($os->getBreed());
        $new_os->setRelease($os->getRelease());
        $this->osRepository->add($new_os, true);
    }    
}
