<?php

namespace App\Tests;

use App\Repository\InstanceTypesRepository;
use App\Repository\HardwareProfilesRepository;
use App\Repository\OperatingSystemsRepository;
use App\Entity\HardwareProfiles;
use App\Entity\InstanceTypes;
use App\Entity\OperatingSystems;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InstanceTypesTest extends KernelTestCase
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
//    private $dummy = array('name' => 'Dummy');

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

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->hpRepository = $this->entityManager->getRepository(HardwareProfiles::class);
        $this->itRepository = $this->entityManager->getRepository(InstanceTypes::class);
        $this->osRepository = $this->entityManager->getRepository(OperatingSystems::class);
    }

    /**
     * 
     * @return array<InstanceTypes>
     */
    public function testInstanceTypesListIsNotEmpty(): array {

        $instance_types = $this->itRepository->findAll();
        $this->assertNotEmpty($instance_types);
        return $instance_types;
    }

    
    /**
     * @depends testInstanceTypesListIsNotEmpty
     * @param array<InstanceTypes> $instance_types
     * @return void
     */
    public function testCanNotAddDuplicateInstanceTypes( array $instance_types): void {

        $it = $instance_types[0];

        $hp = $this->hpRepository->findOneById($it->getHwProfile()->getId());
        $this->assertNotNull($hp);
        $os = $this->osRepository->findOneById($it->getOs()->getId());
        $this->assertNotNull($os);
        
        $new_it = new InstanceTypes();
        $new_it->setOs($os);
        $new_it->setHwProfile($hp);

        $this->assertFalse($this->itRepository->add($new_it, true));
    }
}
