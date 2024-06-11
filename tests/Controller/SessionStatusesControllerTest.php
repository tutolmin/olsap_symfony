<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\SessionStatuses;
use App\Repository\SessionStatusesRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class SessionStatusesControllerTest extends WebTestCase
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
    private $dummy = array('name'=>'Dummy');
        
    /**
     * 
     * @var SessionStatusesRepository
     */
    private $sessionStatusesRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->sessionStatusesRepository = $this->entityManager->getRepository(SessionStatuses::class);
    }

    /**
     * 
     * @return array<SessionStatuses>
     */
    public function testSessionStatusesListIsNotEmpty(): array {

        $sessionStatuses = $this->sessionStatusesRepository->findAll();
        $this->assertNotEmpty($sessionStatuses);
        return $sessionStatuses;
    }
    
    /**
     * 
     * @depends testSessionStatusesListIsNotEmpty
     * @param array<SessionStatuses> $sessionStatuses
     * @return void
     */
    public function testSessionStatusesWebPageListContainsAllRecords($sessionStatuses): void
    {
        $this->client->request('GET', '/session/statuses/');

        $this->assertResponseIsSuccessful();
        
        foreach ($sessionStatuses as $t) {

            $session = $this->sessionStatusesRepository->findOneById($t);
            $this->assertNotNull($session);

            $this->assertAnySelectorTextContains('table tr td', $session->getStatus());
        }        
    }
    
    /**
     * 
     * @depends testSessionStatusesListIsNotEmpty
     * @param array<SessionStatuses> $sessionStatuses
     * @return void
     */
    public function testSessionStatusDisplayWebPageContainsData($sessionStatuses): void {
        
        foreach ($sessionStatuses as $t) {

            $session = $this->sessionStatusesRepository->findOneById($t);
            $this->assertNotNull($session);
            
            $this->client->request('GET', '/session/statuses/' . $session->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $session->getStatus());
        }
    }
    
    /**
     * 
     * @depends testSessionStatusesListIsNotEmpty
     * @param array<SessionStatuses> $sessionStatuses
     * @return void
     */
    public function testCanRemoveAllSessionStatusesByClickingDeleteButton(array $sessionStatuses): void {

        foreach ($sessionStatuses as $t) {

            $item = $this->sessionStatusesRepository->findOneById($t);
            $this->assertNotNull($item);
            $id = $item->getId();

            $crawler = $this->client->request('GET', '/session/statuses/' . $item->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_item = $this->sessionStatusesRepository->findOneById($id);
            $this->assertNull($removed_item);            
        }
    }
    
    /**
     * 
     * @return void
     */
    public function testCanAddDummySessionStatusBySubmittingForm(): void {

        $crawler = $this->client->request('GET', '/session/statuses/new');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Save');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['session_statuses[status]'] = $this->dummy['name'];

        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->sessionStatusesRepository->findOneByStatus($this->dummy['name']);
        $this->assertNotNull($item);        
    }    
}
