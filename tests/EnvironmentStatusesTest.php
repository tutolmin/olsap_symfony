<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\EnvironmentStatuses;
use App\Repository\EnvironmentStatusesRepository;
use App\Service\EnvironmentStatusesManager;

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

    private EnvironmentStatusesManager $environmentStatusesManager;

    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->environmentStatusesRepository = $this->entityManager->getRepository(EnvironmentStatuses::class);
        $this->environmentStatusesManager = static::getContainer()->get(EnvironmentStatusesManager::class);        
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
     * 
     * @depends testEnvironmentStatusesListIsNotEmpty
     * @param array<EnvironmentStatuses> $environment_statuses
     * @return void
     */
    public function testCanRemoveAllEnvironmentStatuses($environment_statuses): void {
    
         foreach ($environment_statuses as $s) {
            
            $item = $this->environmentStatusesRepository->findOneById($s);
            $this->assertNotNull($item);
            $id = $item->getId();

            $this->environmentStatusesManager->removeEnvironmentStatus($item);
            
            $removed_item = $this->environmentStatusesRepository->findOneById($id);
            $this->assertNull($removed_item);
        }
    }
}
