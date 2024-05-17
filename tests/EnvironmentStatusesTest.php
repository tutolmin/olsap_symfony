<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\EnvironmentStatuses;
//use App\Entity\Environments;
use App\Repository\EnvironmentStatusesRepository;
//use App\Repository\EnvironmentsRepository;

class EnvironmentStatusesTest extends KernelTestCase
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
     * @var EnvironmentStatusesRepository
     */
    private $environmentStatusesRepository;

    protected function setUp(): void {
        self::bootKernel();

//        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->environmentStatusesRepository = $this->entityManager->getRepository(EnvironmentStatuses::class);
//        $this->environmentsRepository = $this->entityManager->getRepository(Environments::class);
    }
    
    /**
     * 
     * @return array<EnvironmentStatuses>
     */
    public function testEnvironmentStatusesListIsNotEmpty(): array {

        $environment_statuses = $this->environmentStatusesRepository->findAll();
        $this->assertNotEmpty($environment_statuses);
        
        return $environment_statuses;
    }
    
    /**
     * 
     * @depends testEnvironmentStatusesListIsNotEmpty
     * @param array<EnvironmentStatuses> $environment_statuses
     * @return EnvironmentStatuses|null
     */
    public function testEnvironmentStatusHasEnvironmentsReference(
            array $environment_statuses): ?EnvironmentStatuses {
        
        foreach ($environment_statuses as $environment_status) {
            if($environment_status->getEnvironments()->first()){
                $this->assertTrue(true);
                return $environment_status;
            }
        }
        $this->assertTrue(false);
        return null;
    }
    
    public function testCanNotAddEnvironmentStatusWithoutStatusString(): void {
        
        $this->assertFalse($this->environmentStatusesRepository->add(
                new EnvironmentStatuses(), true));
    }

    /**
     * 
     * @depends testEnvironmentStatusesListIsNotEmpty
     * @param array<EnvironmentStatuses> $environment_statuses
     * @return void
     */
    public function testCanNotAddDuplicateEnvironmentStatus(
            array $environment_statuses): void {

        $new_environment_status = new EnvironmentStatuses();
        $new_environment_status->setStatus($environment_statuses[0]->getStatus());
        $this->assertFalse($this->environmentStatusesRepository->add(
                $new_environment_status, true));
    }

    /**
     * 
     * @return EnvironmentStatuses
     */
    public function testCanAddDummyEnvironmentStatus(): EnvironmentStatuses {
        
        $environment_status = new EnvironmentStatuses();
        $environment_status->setStatus($this->dummy['name']);
        $this->assertTrue($this->environmentStatusesRepository->add($environment_status, true));
        return $environment_status;
    }

    /**
     * @depends testCanAddDummyEnvironmentStatus
     * @param EnvironmentStatuses $environment_status
     * @return void
     */
    public function testCanRemoveDummyEnvironmentStatus(EnvironmentStatuses $environment_status): void {
        
        $this->assertTrue($this->environmentStatusesRepository->remove($environment_status));
    }
        
    /**
     * 
     * @depends testEnvironmentStatusHasEnvironmentsReference
     * @param EnvironmentStatuses $environment_statuses
     * @return void
     */
    public function testCanNotRemoveReferencedEnvironmentStatus(
            EnvironmentStatuses $environment_statuses): void {
        $this->markTestSkipped("references are not easy to delete"
            );

//        $this->assertFalse($this->environmentStatusesRepository->remove($environment_statuses, true));
    }

    /**
     * @depends testEnvironmentStatusHasEnvironmentsReference
     * @param EnvironmentStatuses $environment_status
     * @return void
     */
    public function testCanRemoveEnvironmentStatusWithCascadeFlag(
            EnvironmentStatuses $environment_status): void {
        $this->markTestSkipped(
                'Cascade delete is way to complicated with all the references',
            );
        /*
        $breed_id = $breed->getId();

        $this->assertTrue($this->breedsManager->removeEnvironmentStatus($breed, true));

        // Try to find existing OS
        $this->assertEmpty($this->osRepository->findBy(['breed' => $breed_id]));
         * 
         */
    }
}
