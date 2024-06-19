<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TasksRepository;
use App\Entity\Tasks;
use App\Entity\TaskTechs;
use App\Repository\TaskTechsRepository;
use App\Entity\Technologies;
use App\Repository\TechnologiesRepository;
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
     * @var array<string>
     */
    private $dummy = array('name'=>'Dummy', 'path'=>'dummy');
        
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
     * @var TechnologiesRepository
     */
    private $technologiesRepository;

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
        $this->technologiesRepository = $this->entityManager->getRepository(Technologies::class);
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
        
        $tech = $this->technologiesRepository->findOneBy([]);
        $this->assertNotNull($tech);
        
        $crawler = $this->client->request('GET', '/task/techs/new');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Save');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['task_techs[task]'] = strval($task->getId());
        $form['task_techs[tech]'] = strval($tech->getId());

        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->taskTechsRepository->findOneBy(
                ['task' => $task->getId(), 'tech' => $tech->getId()]);
        $this->assertNotNull($item);        
    }
    
    /**
     * @depends testTaskTechsListIsNotEmpty
     * @param array<TaskTechs> $taskTechs
     * @return void
     */
    public function testCanEditTaskTechBySubmittingForm($taskTechs): void {
        
        $task = $this->addDummyTask();
        $this->assertNotNull($task);
        
        $taskTech = $this->taskTechsRepository->findOneById($taskTechs[0]);
        $this->assertNotNull($taskTech);
            
        $crawler = $this->client->request('GET', '/task/techs/'.$taskTech->getId().'/edit');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Update');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['task_techs[task]'] = strval($task->getId());
        $form['task_techs[tech]'] = strval($taskTech->getTech()->getId());

        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->taskTechsRepository->findOneBy(
                ['task' => $task->getId(), 'tech' => $taskTech->getTech()]);
        $this->assertNotNull($item);        
    }      
}
