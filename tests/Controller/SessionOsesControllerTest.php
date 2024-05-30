<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\SessionOses;
use App\Repository\SessionOsesRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class SessionOsesControllerTest extends WebTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    
    /**
     * 
     * @var SessionOsesRepository
     */
    private $sessionOsesRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->sessionOsesRepository = $this->entityManager->getRepository(SessionOses::class);
    }

    /**
     * 
     * @return array<SessionOses>
     */
    public function testSessionOsesListIsNotEmpty(): array {

        $sessionOses = $this->sessionOsesRepository->findAll();
        $this->assertNotEmpty($sessionOses);
        return $sessionOses;
    }
    
    /**
     * 
     * @depends testSessionOsesListIsNotEmpty
     * @param array<SessionOses> $sessionOses
     * @return void
     */
    public function testSessionOsesWebPageListContainsAllRecords($sessionOses): void
    {
        $this->client->request('GET', '/session/oses/');

        $this->assertResponseIsSuccessful();
        
        foreach ($sessionOses as $t) {

            $session_os = $this->sessionOsesRepository->findOneById($t);
            $this->assertNotNull($session_os);

            $this->assertAnySelectorTextContains('table tr td', $session_os->getSession());
        }        
    }
    
    /**
     * 
     * @depends testSessionOsesListIsNotEmpty
     * @param array<SessionOses> $sessionOses
     * @return void
     */
    public function testSessionOsDisplayWebPageContainsData($sessionOses): void {
        
        foreach ($sessionOses as $t) {

            $session = $this->sessionOsesRepository->findOneById($t);
            $this->assertNotNull($session);
            
            $this->client->request('GET', '/session/oses/' . $session->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $session->getOs());
        }
    }
    
    /**
     * 
     * @depends testSessionOsesListIsNotEmpty
     * @param array<SessionOses> $sessionOses
     * @return void
     */
    public function testCanRemoveAllSessionOsesByClickingDeleteButton(array $sessionOses): void {

        foreach ($sessionOses as $t) {

            $session = $this->sessionOsesRepository->findOneById($t);
            $this->assertNotNull($session);
            $id = $session->getId();

            $crawler = $this->client->request('GET', '/session/oses/' . $session->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_session = $this->sessionOsesRepository->findOneById($id);
            $this->assertNull($removed_session);            
        }
    }
}
