<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\SessionTechs;
use App\Repository\SessionTechsRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class SessionTechsControllerTest extends WebTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    
    /**
     * 
     * @var SessionTechsRepository
     */
    private $sessionTechsRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->sessionTechsRepository = $this->entityManager->getRepository(SessionTechs::class);
    }

    /**
     * 
     * @return array<SessionTechs>
     */
    public function testSessionTechsListIsNotEmpty(): array {

        $sessionTechs = $this->sessionTechsRepository->findAll();
        $this->assertNotEmpty($sessionTechs);
        return $sessionTechs;
    }
    
    /**
     * 
     * @depends testSessionTechsListIsNotEmpty
     * @param array<SessionTechs> $sessionTechs
     * @return void
     */
    public function testSessionTechsWebPageListContainsAllRecords($sessionTechs): void
    {
        $this->client->request('GET', '/session/techs/');

        $this->assertResponseIsSuccessful();
        
        foreach ($sessionTechs as $t) {

            $session_os = $this->sessionTechsRepository->findOneById($t);
            $this->assertNotNull($session_os);

            $this->assertAnySelectorTextContains('table tr td', $session_os->getSession());
        }        
    }
    
    /**
     * 
     * @depends testSessionTechsListIsNotEmpty
     * @param array<SessionTechs> $sessionTechs
     * @return void
     */
    public function testsessionTechDisplayWebPageContainsData($sessionTechs): void {
        
        foreach ($sessionTechs as $t) {

            $session = $this->sessionTechsRepository->findOneById($t);
            $this->assertNotNull($session);
            
            $this->client->request('GET', '/session/techs/' . $session->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $session->getTech());
        }
    }
    
    /**
     * 
     * @depends testSessionTechsListIsNotEmpty
     * @param array<SessionTechs> $sessionTechs
     * @return void
     */
    public function testCanRemoveAllSessionTechsByClickingDeleteButton(array $sessionTechs): void {

        foreach ($sessionTechs as $t) {

            $session = $this->sessionTechsRepository->findOneById($t);
            $this->assertNotNull($session);
            $id = $session->getId();

            $crawler = $this->client->request('GET', '/session/techs/' . $session->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_session = $this->sessionTechsRepository->findOneById($id);
            $this->assertNull($removed_session);            
        }
    }
}
