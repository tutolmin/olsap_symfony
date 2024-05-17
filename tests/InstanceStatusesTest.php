<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\InstanceStatuses;
//use App\Entity\Instances;
use App\Repository\InstanceStatusesRepository;
//use App\Repository\InstancesRepository;

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

    protected function setUp(): void {
        self::bootKernel();

//        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->instanceStatusesRepository = $this->entityManager->getRepository(InstanceStatuses::class);
//        $this->instancesRepository = $this->entityManager->getRepository(Instances::class);
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
    
    /**
     * 
     * @depends testInstanceStatusesListIsNotEmpty
     * @param array<InstanceStatuses> $instance_statuses
     * @return InstanceStatuses|null
     */
    public function testInstanceStatusHasInstancesReference(
            array $instance_statuses): ?InstanceStatuses {
        
        foreach ($instance_statuses as $instance_status) {
            if($instance_status->getInstances()->first()){
                $this->assertTrue(true);
                return $instance_status;
            }
        }
        $this->assertTrue(false);
        return null;
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
     * @depends testCanAddDummyInstanceStatus
     * @param InstanceStatuses $instance_status
     * @return void
     */
    public function testCanRemoveDummyInstanceStatus(InstanceStatuses $instance_status): void {
        
        $this->assertTrue($this->instanceStatusesRepository->remove($instance_status));
    }
           
    /**
     * 
     * @depends testInstanceStatusHasInstancesReference
     * @param InstanceStatuses $instance_statuses
     * @return void
     */
    public function testCanNotRemoveReferencedInstanceStatus(
            InstanceStatuses $instance_statuses): void {
        $this->markTestSkipped("references are not easy to delete"
            );
//        $this->assertTrue($this->instanceStatusesRepository->remove($instance_statuses, true));
    }

    
    /**
     * @depends testInstanceStatusHasInstancesReference
     * @param InstanceStatuses $instance_status
     * @return void
     */
    public function testCanRemoveInstanceStatusWithCascadeFlag(
            InstanceStatuses $instance_status): void {
        $this->markTestSkipped(
                'Cascade delete is way to complicated with all the references',
            );
        /*
        $breed_id = $breed->getId();

        $this->assertTrue($this->breedsManager->removeInstanceStatus($breed, true));

        // Try to find existing OS
        $this->assertEmpty($this->osRepository->findBy(['breed' => $breed_id]));
         * 
         */
    }
}
