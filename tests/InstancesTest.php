<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Instances;
use App\Entity\InstanceTypes;
use App\Entity\InstanceStatuses;
use App\Repository\InstancesRepository;
use App\Repository\InstanceTypesRepository;
use App\Repository\InstanceStatusesRepository;
use App\Service\LxcManager;

class InstancesTest extends KernelTestCase
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
    private $dummy = array('name'=>'dummy');
    
    /**
     * 
     * @var InstancesRepository
     */
    private $instancesRepository;

    /**
     * 
     * @var InstanceTypesRepository
     */
    private $instanceTypesRepository;
        
    /**
     * 
     * @var InstanceStatusesRepository
     */
    private $instancesStatusesRepository;
    
    /**
     * 
     * @var LxcManager
     */
    private $lxdService;
    
    protected function setUp(): void {
        self::bootKernel();

//        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->instancesRepository = $this->entityManager->getRepository(Instances::class);
        $this->instanceTypesRepository = $this->entityManager->getRepository(InstanceTypes::class);
        $this->instancesStatusesRepository = $this->entityManager->getRepository(InstanceStatuses::class);

        $this->lxdService = static::getContainer()->get(LxcManager::class);        
    }
    
    /**
     * 
     * @return array<Instances>
     */
    public function testInstancesListIsNotEmpty(): array {

        $instances = $this->instancesRepository->findAll();
        $this->assertNotEmpty($instances);
        return $instances;
    }

    public function testCanNotAddInstanceWithoutMandatoryFields(): void {
        
        $this->assertFalse($this->instancesRepository->add(new Instances(), true));
    }
    
    /**
     * 
     * @return Instances
     */
    public function testCanAddDummyInstance(): Instances {

        $instanceStatus = $this->instancesStatusesRepository->findOneByStatus('New');
        $this->assertNotNull($instanceStatus);

        $instanceTypes = $this->instanceTypesRepository->findOneBy(array());
        $this->assertNotNull($instanceTypes);

        $instance = new Instances();
        $instance->setName($this->dummy['name']);
        $instance->setStatus($instanceStatus);
        $instance->setInstanceType($instanceTypes);
        $instance->setCreatedAt(new \DateTimeImmutable('now'));
        
        $this->assertTrue($this->instancesRepository->add($instance, true));
        
        return $instance;
    }
    
    /**
     * 
     * @depends testInstancesListIsNotEmpty
     * @param array<Instances> $instances
     * @return void
     */
    public function testInstanceListHasNoOrphans($instances) {
        foreach ($instances as $instance) {
            $this->assertNotNull($this->lxdService->getObjectInfo($instance->getName()));
        }
    }

    /**
     * 
     * @depends testInstancesListIsNotEmpty
     * @return void
     */
    public function testLxcInventoryHasNoOrphans() {

        // Use Lxc service method
        $objects = $this->lxdService->getObjectList();
        $this->assertNotNull($objects);

        foreach ($objects as $object) {
            $info = $this->lxdService->getObjectInfo($object);
            $this->assertNotNull($info);

            if (array_key_exists('config', $info) && is_array($info['config'])) {
                $this->assertNotNull($this->instancesRepository->findOneByName($info['name']));
            }
        }
    }

    /**
     * 
     * @depends testInstancesListIsNotEmpty
     * @param array<Instances> $instances
     * @return void
    public function testCanRemoveAllInstances(array $instances): void { 

        foreach ($instances as $s) {
            
            $instance = $this->instancesRepository->findOneById($s);
            $this->assertNotNull($instance);
            $id = $instance->getId();

            $this->lxcManager->removeInstance($instance);
            
            $removed_instance = $this->instancesRepository->findOneById($id);
            $this->assertNull($removed_instance);
        }
    }

    /**
     * @depends testInstancesListIsNotEmpty
     * @param array<Instances> $instances
     * @return void
     */
    public function testCanNotAddDuplicateInstance( array $instances): void {
                
        $existing_record = $instances[0];

        $instanceStatus = $this->instancesStatusesRepository->findOneById($existing_record->getStatus()->getId());
        $this->assertNotNull($instanceStatus);
              
        $instanceTypes = $this->instanceTypesRepository->findOneById($existing_record->getInstanceType()->getId());
        $this->assertNotNull($instanceTypes);
      
        $instance = new Instances();
        $instance->setName($existing_record->getName());
        $instance->setStatus($instanceStatus);
        $instance->setInstanceType($instanceTypes);
        $instance->setCreatedAt(new \DateTimeImmutable('now'));
        
        $this->assertFalse($this->instancesRepository->add($instance, true));
    }
}
