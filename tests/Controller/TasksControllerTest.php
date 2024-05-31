<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tasks;
use App\Repository\TasksRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class TasksControllerTest extends WebTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    
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
        $this->tasksRepository = $this->entityManager->getRepository(Tasks::class);
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
    
    /**
     * 
     * @depends testTasksListIsNotEmpty
     * @param array<Tasks> $tasks
     * @return void
     */
    public function testTasksWebPageListContainsAllRecords($tasks): void
    {
        $this->client->request('GET', '/tasks/');

        $this->assertResponseIsSuccessful();
        
        foreach ($tasks as $t) {

            $task = $this->tasksRepository->findOneById($t);
            $this->assertNotNull($task);

            $this->assertAnySelectorTextContains('table tr td', $task->getName());
        }        
    }
    
    /**
     * 
     * @depends testTasksListIsNotEmpty
     * @param array<Tasks> $tasks
     * @return void
     */
    public function testTaskDisplayWebPageContainsData($tasks): void {
        
        foreach ($tasks as $t) {

            $task = $this->tasksRepository->findOneById($t);
            $this->assertNotNull($task);
            
            $this->client->request('GET', '/tasks/' . $task->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $task->getPath());
        }
    }
    
    /**
     * 
     * @depends testTasksListIsNotEmpty
     * @param array<Tasks> $tasks
     * @return void
     */
    public function testCanRemoveAllTasksByClickingDeleteButton(array $tasks): void {

        foreach ($tasks as $t) {

            $task = $this->tasksRepository->findOneById($t);
            $this->assertNotNull($task);
            $id = $task->getId();

            $crawler = $this->client->request('GET', '/tasks/' . $task->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_task = $this->tasksRepository->findOneById($id);
            $this->assertNull($removed_task);            
        }
    }
}
