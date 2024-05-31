<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tasks;
use App\Repository\TasksRepository;
use App\Service\TasksManager;

class TasksTest extends KernelTestCase
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
    private $dummy = array('name'=>'Dummy', 'path'=>'dummy');
    
    /**
     * 
     * @var TasksRepository
     */
    private $tasksRepository;
    
    private TasksManager $tasksManager;

    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->tasksRepository = $this->entityManager->getRepository(Tasks::class);
        $this->tasksManager = static::getContainer()->get(TasksManager::class);        
    }
    
    /**
     * 
     * @return array<Tasks>
     */
    public function testTasksListIsNotEmpty(): array {

        $tasks = $this->tasksRepository->findAll();
        $this->assertNotEmpty($tasks);
        
        return $tasks;
    }
    
    public function testCanNotAddTaskWithoutName(): void {
        
        $task = new Tasks();
        $task->setPath($this->dummy['path']);
        
        $this->assertFalse($this->tasksRepository->add($task, true));
    }
    
    public function testCanNotAddTaskWithoutPath(): void {
        
        $task = new Tasks();
        $task->setName($this->dummy['name']);
        
        $this->assertFalse($this->tasksRepository->add($task, true));
    }

    /**
     * @depends testTasksListIsNotEmpty
     * @param array<Tasks> $tasks
     * @return void
     */
    public function testCanNotAddDuplicateTasks( array $tasks): void {

        $task = $this->tasksRepository->findOneById($tasks[0]->getId());
        $this->assertNotNull($task);

        $new_task = new Tasks();
        $new_task->setName($task->getName());
        $new_task->setPath($task->getPath());

        $this->assertFalse($this->tasksRepository->add($new_task, true));
    }    
    
    /**
     * 
     * @return Tasks
     */
    public function testCanAddDummyTask(): Tasks {
        
        $task = new Tasks();
        $task->setName($this->dummy['name']);
        $task->setPath($this->dummy['path']);
        $this->assertTrue($this->tasksRepository->add($task, true));
        return $task;
    }

    /**
     * @depends testTasksListIsNotEmpty
     * @param array<Tasks> $tasks
     * @return void
     */
    public function testCanRemoveAllTasks(array $tasks): void {

        foreach ($tasks as $s) {

            $task = $this->tasksRepository->findOneById($s);
            $this->assertNotNull($task);
            $id = $task->getId();

            $this->tasksManager->removeTask($task);

            $removed_task = $this->tasksRepository->findOneById($id);
            $this->assertNull($removed_task);
        }
    }
}
