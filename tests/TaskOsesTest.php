<?php

namespace App\Tests;

use App\Repository\TaskOsesRepository;
use App\Repository\TasksRepository;
use App\Repository\OperatingSystemsRepository;
use App\Entity\Tasks;
use App\Entity\TaskOses;
use App\Entity\OperatingSystems;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskOsesTest extends KernelTestCase
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
    private $dummy = array('name' => 'Dummy', 'path'=>'dummy');

    /**
     * 
     * @var TasksRepository
     */
    private $tasksRepository;

    /**
     * 
     * @var TaskOsesRepository
     */
    private $toRepository;

    /**
     * 
     * @var OperatingSystemsRepository
     */
    private $osRepository;

    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->tasksRepository = $this->entityManager->getRepository(Tasks::class);
        $this->toRepository = $this->entityManager->getRepository(TaskOses::class);
        $this->osRepository = $this->entityManager->getRepository(OperatingSystems::class);
    }

    /**
     * 
     * @return array<TaskOses>
     */
    public function testTaskOsesListIsNotEmpty(): array {

        $task_oses = $this->toRepository->findAll();
        $this->assertNotEmpty($task_oses);
        return $task_oses;
    }
     
    /**
     * @depends testTaskOsesListIsNotEmpty
     * @return void
     */
    public function testCanRemoveAllTaskOses(): void {
    
        $this->toRepository->deleteAll();
        
        $task_oses = $this->toRepository->findAll();
        $this->assertEmpty($task_oses);        
    }
    
    /**
     * @depends testTaskOsesListIsNotEmpty
     * @param array<TaskOses> $task_oses
     * @return void
     */
    public function testCanNotAddTaskOsWithoutTask(array $task_oses): void {

        $to = $task_oses[0];

        $task = $this->tasksRepository->findOneById($to->getTask()->getId());
        $this->assertNotNull($task);

        $new_to = new TaskOses();
        $new_to->setTask($task);

        $this->assertFalse($this->toRepository->add($new_to, true));
    }

    /**
     * @depends testTaskOsesListIsNotEmpty
     * @param array<TaskOses> $task_oses
     * @return void
     */
    public function testCanNotAddTaskOsWithoutOperatingSystem(array $task_oses): void {

        $to = $task_oses[0];

        $os = $this->osRepository->findOneById($to->getOs()->getId());
        $this->assertNotNull($os);

        $new_to = new TaskOses();
        $new_to->setOs($os);

        $this->assertFalse($this->toRepository->add($new_to, true));
    }
    
    /**
     * @depends testTaskOsesListIsNotEmpty
     * @param array<TaskOses> $task_oses
     * @return void
     */
    public function testCanNotAddDuplicateTaskOses( array $task_oses): void {

        $to = $task_oses[0];

        $task = $this->tasksRepository->findOneById($to->getTask()->getId());
        $this->assertNotNull($task);
        $os = $this->osRepository->findOneById($to->getOs()->getId());
        $this->assertNotNull($os);
        
        $new_to = new TaskOses();
        $new_to->setTask($task);
        $new_to->setOs($os);

        $this->assertFalse($this->toRepository->add($new_to, true));
    }

    /**
     * 
     * @return Tasks
     */
    private function addDummyTask(): Tasks {
            
        $task = new Tasks();
        $task->setName($this->dummy['name']);
        $task->setPath($this->dummy['path']);
        $this->assertTrue($this->tasksRepository->add($task, true));
        
        return $task;
    }
    
    /**
     * @depends testTaskOsesListIsNotEmpty
     * @param array<TaskOses> $task_oses
     * @return TaskOses
     */
    public function testCanAddDummyTaskOs($task_oses): TaskOses {

        $task = $this->addDummyTask();

        $to = $task_oses[0];
        $os = $this->osRepository->findOneById($to->getOs()->getId());
        $this->assertNotNull($os);
        
        $taskOses = new TaskOses();
        $taskOses->setTask($task);
        $taskOses->setOs($os);
        $this->assertTrue($this->toRepository->add($taskOses, true));

        return $taskOses;
    }       
}
