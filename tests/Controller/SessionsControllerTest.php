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
}
