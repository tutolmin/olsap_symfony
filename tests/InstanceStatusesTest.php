<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\InstanceStatuses;
use App\Entity\Instances;
use App\Repository\InstanceStatusesRepository;
use App\Repository\InstancesRepository;
use App\Service\InstanceStatusesManager;

class InstanceStatusesTest extends KernelTestCase
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
     * @var InstanceStatusesRepository
     */
    private $instanceStatusesRepository;
    
    /**
     * 
     * @var InstancesRepository
     */
    private $instancesRepository;

    private InstanceStatusesManager $instanceStatusesManager;

    protected function setUp(): void {
        self::bootKernel();

//        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->instanceStatusesRepository = $this->entityManager->getRepository(InstanceStatuses::class);
        $this->instanceStatusesManager = static::getContainer()->get(InstanceStatusesManager::class);        
        $this->instancesRepository = $this->entityManager->getRepository(Instances::class);
    }
    
    /**
     * 
     * @return array<InstanceStatuses>
     */
    public function testInstanceStatusesListIsNotEmpty(): array {

        $instance_statuses = $this->instanceStatusesRepository->findAll();
        $this->assertNotEmpty($instance_statuses);
        
        return $instance_statuses;
    }
    
    public function testCanNotAddInstanceStatusWithoutStatusString(): void {
        
        $this->assertFalse($this->instanceStatusesRepository->add(
                new InstanceStatuses(), true));
    }

    /**
     * 
     * @depends testInstanceStatusesListIsNotEmpty
     * @param array<InstanceStatuses> $instance_statuses
     * @return void
     */
    public function testCanNotAddDuplicateInstanceStatus(
            array $instance_statuses): void {

        $new_instance_status = new InstanceStatuses();
        $new_instance_status->setStatus($instance_statuses[0]->getStatus());
        $this->assertFalse($this->instanceStatusesRepository->add(
                $new_instance_status, true));
    }

    public function testCanAddDummyInstanceStatus(): InstanceStatuses {
        
        $instance_status = new InstanceStatuses();
        $instance_status->setStatus($this->dummy['name']);
        $this->assertTrue($this->instanceStatusesRepository->add($instance_status, true));
        return $instance_status;
    }

    /**
     * 
     * @depends testInstanceStatusesListIsNotEmpty
     * @param array<InstanceStatuses> $instance_statuses
     * @return void
     */
    public function testCanRemoveAllInstanceStatuses($instance_statuses): void {

        foreach ($instance_statuses as $s) {

            $item = $this->instanceStatusesRepository->findOneById($s);
            $this->assertNotNull($item);
            $id = $item->getId();

            $this->instanceStatusesManager->removeInstanceStatus($item);

            $removed_item = $this->instanceStatusesRepository->findOneById($id);
            $this->assertNull($removed_item);
        }

        $this->assertEmpty($this->instancesRepository->findAll());
    }
}
