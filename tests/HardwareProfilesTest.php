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
#use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
#use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class HardwareProfilesTest extends KernelTestCase
{    
    /**
     * 
     * @var EntityManagerInterface
     */
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
     * @var InstanceTypesRepository
     */
    private $itRepository;

    /**
     * 
     * @var OperatingSystemsRepository
     */
    private $osRepository;
    
    protected function setUp(): void {
        self::bootKernel();
    
        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
        $this->hpRepository = $this->entityManager->getRepository( HardwareProfiles::class);
        $this->itRepository = $this->entityManager->getRepository(InstanceTypes::class);
        $this->osRepository = $this->entityManager->getRepository(OperatingSystems::class);
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
     * @param array<HardwareProfiles> $hp
     * @depends testSupportedHardwareProfilesListIsNotEmpty
     * @return void
     */
    public function testEachSupportedHardwareProfileHasCorrespondingInstanceTypesForAllSupportedOs( array $hp): void {

        // Get the supported OS list
	$oses = $this->osRepository->findBySupported(1);
        $this->assertNotEmpty($oses);

        // Iterate through all the HPs
        foreach ($hp as &$hw_profile) {

            foreach ($oses as &$os) {

            // Try to find existing Instance type
            $it = $this->itRepository->findBy(['os' => $os->getId(), 'hw_profile' => $hw_profile->getId()]);

            $this->assertNotEmpty($it);

            }
        }
    }
    
    public function testCanNotAddHardwareProfileWithoutNameOrType(): void {
        
        #$this->expectException(NotNullConstraintViolationException::class);
        $this->assertFalse($this->hpRepository->add(new HardwareProfiles(), true));
    }
    
    /**
     * @return void
     */    
    public function testCanAddAndRemoveDummyHardwareProfile(): void {

        $domain = $this->hpRepository->findOneBy(array());
        $this->assertNotNull($domain);

        $hp = new HardwareProfiles();
        $hp->setName($this->dummy['name']);
        $hp->setSupported(false);
        $hp->setType(false);
        $this->assertTrue($this->hpRepository->add($hp, true));
        $this->hpRepository->remove($hp, true);
    }

    /**
     * @depends testSupportedHardwareProfilesListIsNotEmpty
     * @return void
     */
    public function testCanAddAndRemoveDummySupportedHardwareProfile(): void {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'This test has not been implemented yet.',
        );
    }
    
    /**
     * @depends testHardwareProfilesListIsNotEmpty
     * @return void
     */
    public function testCanNotAddDuplicateHardwareProfile(): void {
        
#        $this->expectException(UniqueConstraintViolationException::class);
        
        $hp = $this->hpRepository->findOneBy(array());
        $this->assertNotNull($hp);
      
        $new_hp = new HardwareProfiles();
        $new_hp->setName($hp->getName());
        $new_hp->setSupported($hp->isSupported());
        $new_hp->setType($hp->isType());
        $this->assertFalse($this->hpRepository->add($new_hp, true));
    }
}
