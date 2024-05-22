<?php

namespace App\Tests;

use App\Repository\TaskInstanceTypesRepository;
use App\Repository\TasksRepository;
use App\Repository\InstanceTypesRepository;
use App\Entity\Tasks;
use App\Entity\TaskInstanceTypes;
use App\Entity\InstanceTypes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskInstanceTypesTest extends KernelTestCase
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
     * @var TasksRepository
     */
    private $tasksRepository;

    /**
     * 
     * @var TaskInstanceTypesRepository
     */
    private $ttRepository;

    /**
     * 
     * @var InstanceTypesRepository
     */
    private $itRepository;

    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->tasksRepository = $this->entityManager->getRepository(Tasks::class);
        $this->ttRepository = $this->entityManager->getRepository(TaskInstanceTypes::class);
        $this->itRepository = $this->entityManager->getRepository(InstanceTypes::class);
    }

    /**
     * 
     * @return array<TaskInstanceTypes>
     */
    public function testTaskInstanceTypesListIsNotEmpty(): array {

        $task_instance_types = $this->ttRepository->findAll();
        $this->assertNotEmpty($task_instance_types);
        return $task_instance_types;
    }

    /**
     * @depends testTaskInstanceTypesListIsNotEmpty
     * @param array<TaskInstanceTypes> $task_instance_types
     * @return void
     */
    public function testCanNotAddTaskInstanceTypeWithoutTask(array $task_instance_types): void {

        $tt = $task_instance_types[0];

        $task = $this->tasksRepository->findOneById($tt->getTask()->getId());
        $this->assertNotNull($task);

        $new_tt = new TaskInstanceTypes();
        $new_tt->setTask($task);

        $this->assertFalse($this->ttRepository->add($new_tt, true));
    }

    /**
     * @depends testTaskInstanceTypesListIsNotEmpty
     * @param array<TaskInstanceTypes> $task_instance_types
     * @return void
     */
    public function testCanNotAddTaskInstanceTypeWithoutInstanceType(array $task_instance_types): void {

        $tt = $task_instance_types[0];

        $it = $this->itRepository->findOneById($tt->getInstanceType()->getId());
        $this->assertNotNull($it);

        $new_tt = new TaskInstanceTypes();
        $new_tt->setInstanceType($it);

        $this->assertFalse($this->ttRepository->add($new_tt, true));
    }
    
    /**
     * @depends testTaskInstanceTypesListIsNotEmpty
     * @param array<TaskInstanceTypes> $task_instance_types
     * @return void
     */
    public function testCanNotAddDuplicateTaskInstanceTypes( array $task_instance_types): void {

        $tt = $task_instance_types[0];

        $task = $this->tasksRepository->findOneById($tt->getTask()->getId());
        $this->assertNotNull($task);
        $it = $this->itRepository->findOneById($tt->getInstanceType()->getId());
        $this->assertNotNull($it);
        
        $new_tt = new TaskInstanceTypes();
        $new_tt->setTask($task);
        $new_tt->setInstanceType($it);

        $this->assertFalse($this->ttRepository->add($new_tt, true));
    }
}
