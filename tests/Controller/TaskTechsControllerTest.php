<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->taskTechsRepository = $this->entityManager->getRepository(TaskTechs::class);
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
}
