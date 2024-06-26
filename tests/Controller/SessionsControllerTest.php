<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sessions;
use App\Repository\SessionsRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class SessionsControllerTest extends WebTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    
    /**
     * 
     * @var SessionsRepository
     */
    private $sessionsRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->sessionsRepository = $this->entityManager->getRepository(Sessions::class);
    }

    /**
     * 
     * @return array<Sessions>
     */
    public function testSessionsListIsNotEmpty(): array {

        $sessions = $this->sessionsRepository->findAll();
        $this->assertNotEmpty($sessions);
        return $sessions;
    }
    
    /**
     * 
     * @depends testSessionsListIsNotEmpty
     * @param array<Sessions> $sessions
     * @return void
     */
    public function testSessionsWebPageListContainsAllRecords($sessions): void
    {
        $this->client->request('GET', '/sessions/');

        $this->assertResponseIsSuccessful();
        
        foreach ($sessions as $t) {

            $session = $this->sessionsRepository->findOneById($t);
            $this->assertNotNull($session);

            $this->assertAnySelectorTextContains('table tr td', $session->getHash());
        }        
    }
    
    /**
     * 
     * @depends testSessionsListIsNotEmpty
     * @param array<Sessions> $sessions
     * @return void
     */
    public function testSessionDisplayWebPageContainsData($sessions): void {
        
        foreach ($sessions as $t) {

            $session = $this->sessionsRepository->findOneById($t);
            $this->assertNotNull($session);
            
            $this->client->request('GET', '/sessions/' . $session->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $session->getHash());
        }
    }
    
    /**
     * 
     * @depends testSessionsListIsNotEmpty
     * @param array<Sessions> $sessions
     * @return void
     */
    public function testCanRemoveAllSessionsByClickingDeleteButton(array $sessions): void {

        foreach ($sessions as $t) {

            $session = $this->sessionsRepository->findOneById($t);
            $this->assertNotNull($session);
            $id = $session->getId();

            $crawler = $this->client->request('GET', '/sessions/' . $session->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_session = $this->sessionsRepository->findOneById($id);
            $this->assertNull($removed_session);            
        }
    }
    
    /**
     * 
     * @return void

    public function testCanAddDummyTaskBySubmittingForm(): void {

        $crawler = $this->client->request('GET', '/tasks/new');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Save');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['tasks[name]'] = $this->dummy['name'];
        $form['tasks[path]'] = $this->dummy['path'];

        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->tasksRepository->findOneByPath($this->dummy['path']);
        $this->assertNotNull($item);        
    }
     */
    
    /**
     * @depends testTesteesListIsNotEmpty
     * @param array<Testees> $testees
     * @return void
     */
    /*
    public function testCanEditTesteeBySubmittingForm($testees): void {

        $testee = $this->testeesRepository->findOneById($testees[0]);
        $this->assertNotNull($testee);
            
        $crawler = $this->client->request('GET', '/testees/'.$testee->getId().'/edit');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Update');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['testees[email]'] = $this->dummy['email'];
        $form['testees[oauth_token]'] = $this->dummy['oauth_token'];

        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->testeesRepository->findOneByEmail($this->dummy['email']);
        $this->assertNotNull($item);        
    } 

*/    
}
