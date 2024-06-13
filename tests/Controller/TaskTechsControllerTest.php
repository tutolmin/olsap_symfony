<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TasksRepository;
use App\Entity\Tasks;
use App\Entity\TaskTechs;
use App\Repository\TaskTechsRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class TaskTechsControllerTest extends WebTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    
    /**
     * 
     * @var TaskTechsRepository
     */
    private $taskTechsRepository;

    /**
     * 
     * @var TasksRepository
     */
    private $tasksRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->taskTechsRepository = $this->entityManager->getRepository(TaskTechs::class);
        $this->tasksRepository = $this->entityManager->getRepository(Tasks::class);
    }

    /**
     * 
     * @return array<TaskTechs>
     */
    public function testTaskTechsListIsNotEmpty(): array {

        $taskTechs = $this->taskTechsRepository->findAll();
        $this->assertNotEmpty($taskTechs);
        return $taskTechs;
    }
    
    /**
     * 
     * @depends testTaskTechsListIsNotEmpty
     * @param array<TaskTechs> $taskTechs
     * @return void
     */
    public function testTaskTechsWebPageListContainsAllRecords($taskTechs): void
    {
        $this->client->request('GET', '/task/techs/');

        $this->assertResponseIsSuccessful();
        
        foreach ($taskTechs as $t) {

            $task_os = $this->taskTechsRepository->findOneById($t);
            $this->assertNotNull($task_os);

            $this->assertAnySelectorTextContains('table tr td', $task_os->getTask());
        }        
    }
    
    /**
     * 
     * @depends testTaskTechsListIsNotEmpty
     * @param array<TaskTechs> $taskTechs
     * @return void
     */
    public function testtaskTechDisplayWebPageContainsData($taskTechs): void {
        
        foreach ($taskTechs as $t) {

            $task = $this->taskTechsRepository->findOneById($t);
            $this->assertNotNull($task);
            
            $this->client->request('GET', '/task/techs/' . $task->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $task->getTech());
        }
    }
    
    /**
     * 
     * @depends testTaskTechsListIsNotEmpty
     * @param array<TaskTechs> $taskTechs
     * @return void
     */
    public function testCanRemoveAllTaskTechsByClickingDeleteButton(array $taskTechs): void {

        foreach ($taskTechs as $t) {

            $task = $this->taskTechsRepository->findOneById($t);
            $this->assertNotNull($task);
            $id = $task->getId();

            $crawler = $this->client->request('GET', '/task/techs/' . $task->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_task = $this->taskTechsRepository->findOneById($id);
            $this->assertNull($removed_task);            
        }
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
     * 
     * @return void
     */
    public function testCanAddDummyTaskTechBySubmittingForm(): void {
        
        $task = $this->addDummyTask();
        $this->assertNotNull($task);

        $crawler = $this->client->request('GET', '/tasks/techs/new');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Save');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();
        $tech = $form->get('tech')->getValue();
        
        // set values on a form object
        $form['task_techs[task]'] = $task->getId();

        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->taskTechsRepository->findOneByPath($this->dummy['path']);
        $this->assertNotNull($item);        
    }    
}
