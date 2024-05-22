<?php

namespace App\Tests;

use App\Repository\TaskTechsRepository;
use App\Repository\TasksRepository;
use App\Repository\TechnologiesRepository;
use App\Entity\Tasks;
use App\Entity\TaskTechs;
use App\Entity\Technologies;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTechsTest extends KernelTestCase
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
     * @var TaskTechsRepository
     */
    private $ttRepository;

    /**
     * 
     * @var TechnologiesRepository
     */
    private $techsRepository;

    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->tasksRepository = $this->entityManager->getRepository(Tasks::class);
        $this->ttRepository = $this->entityManager->getRepository(TaskTechs::class);
        $this->techsRepository = $this->entityManager->getRepository(Technologies::class);
    }

    /**
     * 
     * @return array<TaskTechs>
     */
    public function testTaskTechsListIsNotEmpty(): array {

        $task_techs = $this->ttRepository->findAll();
        $this->assertNotEmpty($task_techs);
        return $task_techs;
    }

    /**
     * @depends testTaskTechsListIsNotEmpty
     * @param array<TaskTechs> $task_techs
     * @return void
     */
    public function testCanNotAddTaskTechWithoutTask(array $task_techs): void {

        $tt = $task_techs[0];

        $task = $this->tasksRepository->findOneById($tt->getTask()->getId());
        $this->assertNotNull($task);

        $new_tt = new TaskTechs();
        $new_tt->setTask($task);

        $this->assertFalse($this->ttRepository->add($new_tt, true));
    }

    /**
     * @depends testTaskTechsListIsNotEmpty
     * @param array<TaskTechs> $task_techs
     * @return void
     */
    public function testCanNotAddTaskTechWithoutTechnology(array $task_techs): void {

        $tt = $task_techs[0];

        $technology = $this->techsRepository->findOneById($tt->getTech()->getId());
        $this->assertNotNull($technology);

        $new_tt = new TaskTechs();
        $new_tt->setTech($technology);

        $this->assertFalse($this->ttRepository->add($new_tt, true));
    }
    
    /**
     * @depends testTaskTechsListIsNotEmpty
     * @param array<TaskTechs> $task_techs
     * @return void
     */
    public function testCanNotAddDuplicateTaskTechs( array $task_techs): void {

        $tt = $task_techs[0];

        $task = $this->tasksRepository->findOneById($tt->getTask()->getId());
        $this->assertNotNull($task);
        $technology = $this->techsRepository->findOneById($tt->getTech()->getId());
        $this->assertNotNull($technology);
        
        $new_tt = new TaskTechs();
        $new_tt->setTask($task);
        $new_tt->setTech($technology);

        $this->assertFalse($this->ttRepository->add($new_tt, true));
    }
}
