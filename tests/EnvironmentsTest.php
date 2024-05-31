<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Environments;
use App\Entity\Tasks;
use App\Entity\EnvironmentStatuses;
use App\Repository\EnvironmentsRepository;
use App\Repository\TasksRepository;
use App\Repository\EnvironmentStatusesRepository;
use App\Service\EnvironmentManager;

class EnvironmentsTest extends KernelTestCase
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
    private $dummy = array('hash'=>'dummy');
    
    /**
     * 
     * @var EnvironmentsRepository
     */
    private $environmentsRepository;

    /**
     * 
     * @var TasksRepository
     */
    private $tasksRepository;
        
    /**
     * 
     * @var EnvironmentStatusesRepository
     */
    private $environmentsStatusesRepository;
    
    /**
     * 
     * @var EnvironmentManager
     */
    private $environmentManager;
    
    protected function setUp(): void {
        self::bootKernel();

//        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->environmentsRepository = $this->entityManager->getRepository(Environments::class);
        $this->tasksRepository = $this->entityManager->getRepository(Tasks::class);
        $this->environmentsStatusesRepository = $this->entityManager->getRepository(EnvironmentStatuses::class);

        $this->environmentManager = static::getContainer()->get(EnvironmentManager::class);        
    }
    
    /**
     * 
     * @return array<Environments>
     */
    public function testEnvironmentsListIsNotEmpty(): array {

        $environments = $this->environmentsRepository->findAll();
        $this->assertNotEmpty($environments);
        return $environments;
    }

    public function testCanNotAddEnvironmentWithoutMandatoryFields(): void {
        
        $this->assertFalse($this->environmentsRepository->add(new Environments(), true));
    }
    
    /**
     * 
     * @return Environments
     */
    public function testCanAddDummyEnvironment(): Environments {

        $environmentStatus = $this->environmentsStatusesRepository->findOneByStatus('New');
        $this->assertNotNull($environmentStatus);

        $task = $this->tasksRepository->findOneBy(array());
        $this->assertNotNull($task);

        $environment = new Environments();
        $environment->setHash($this->dummy['hash']);
        $environment->setStatus($environmentStatus);
        $environment->setTask($task);
        
        $this->assertTrue($this->environmentsRepository->add($environment, true));
        
        return $environment;
    }

    /**
     * 
     * @depends testEnvironmentsListIsNotEmpty
     * @param array<Environments> $environments
     * @return void
     */
    public function testCanRemoveAllEnvironments(array $environments): void { 

        foreach ($environments as $s) {
            
            $environment = $this->environmentsRepository->findOneById($s);
            $this->assertNotNull($environment);
            $id = $environment->getId();

            $this->environmentManager->deleteEnvironment($environment);
            
            $removed_environment = $this->environmentsRepository->findOneById($id);
            $this->assertNull($removed_environment);
        }
    }

    /**
     * @depends testEnvironmentsListIsNotEmpty
     * @param array<Environments> $environments
     * @return void
     */
    public function testCanNotAddDuplicateEnvironment( array $environments): void {
                
        $existing_record = $environments[0];

        $environmentStatus = $this->environmentsStatusesRepository->findOneById($existing_record->getStatus()->getId());
        $this->assertNotNull($environmentStatus);
              
        $task = $this->tasksRepository->findOneById($existing_record->getTask()->getId());
        $this->assertNotNull($task);
      
        $environment = new Environments();
        $environment->setHash($existing_record->getHash());
        $environment->setStatus($environmentStatus);
        $environment->setTask($task);
        
        $this->assertFalse($this->environmentsRepository->add($environment, true));
    }
}
