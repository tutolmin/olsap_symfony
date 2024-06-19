<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\SessionOses;
use App\Repository\SessionOsesRepository;
use App\Entity\Sessions;
use App\Entity\Testees;
use App\Entity\SessionStatuses;
use App\Repository\OperatingSystemsRepository;
use App\Entity\OperatingSystems;
use App\Repository\SessionsRepository;
use App\Repository\TesteesRepository;
use App\Repository\SessionStatusesRepository;
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
     * @var array<string>
     */
    private $dummy = array('hash'=>'dummy');
    
    /**
     * 
     * @var SessionsRepository
     */
    private $sessionsRepository;

    /**
     * 
     * @var TesteesRepository
     */
    private $testeesRepository;
        
    /**
     * 
     * @var SessionStatusesRepository
     */
    private $sessionsStatusesRepository;

    /**
     * 
     * @var OperatingSystemsRepository
     */
    private $osRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->sessionOsesRepository = $this->entityManager->getRepository(SessionOses::class);
        $this->sessionsRepository = $this->entityManager->getRepository(Sessions::class);
        $this->testeesRepository = $this->entityManager->getRepository(Testees::class);
        $this->sessionsStatusesRepository = $this->entityManager->getRepository(SessionStatuses::class);
        $this->osRepository = $this->entityManager->getRepository(OperatingSystems::class);
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
   
    /**
     * 
     * @return Sessions
     */
    private function addDummySession(): Sessions {

        $sessionStatus = $this->sessionsStatusesRepository->findOneByStatus('New');
        $this->assertNotNull($sessionStatus);

        $testee = $this->testeesRepository->findOneBy(array());
        $this->assertNotNull($testee);

        $session = new Sessions();
        $session->setHash($this->dummy['hash']);
        $session->setStatus($sessionStatus);
        $session->setTestee($testee);
        $session->setCreatedAt(new \DateTimeImmutable('now'));
        
        $this->assertTrue($this->sessionsRepository->add($session, true));

        return $session;
    }    

    /**
     * 
     * @return void
     */
    public function testCanAddDummySessionOsBySubmittingForm(): void {
        
        $session = $this->addDummySession();
        $this->assertNotNull($session);
        
        $os = $this->osRepository->findOneBy([]);
        $this->assertNotNull($os);
        
        $crawler = $this->client->request('GET', '/session/oses/new');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Save');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['session_oses[session]'] = strval($session->getId());
        $form['session_oses[os]'] = strval($os->getId());

        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->sessionOsesRepository->findOneBy(
                ['session' => $session->getId(), 'os' => $os->getId()]);
        $this->assertNotNull($item);        
    }   
    
    /**
     * @depends testSessionOsesListIsNotEmpty
     * @param array<SessionOses> $sessionOses
     * @return void
     */
    public function testCanEditSessionOsBySubmittingForm($sessionOses): void {
        
        $session = $this->addDummySession();
        $this->assertNotNull($session);
        
        $sessionOs = $this->sessionOsesRepository->findOneById($sessionOses[0]);
        $this->assertNotNull($sessionOs);
            
        $crawler = $this->client->request('GET', '/session/oses/'.$sessionOs->getId().'/edit');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Update');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['session_oses[session]'] = strval($session->getId());
        $form['session_oses[os]'] = strval($sessionOs->getOs()->getId());

        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->sessionOsesRepository->findOneBy(
                ['session' => $session->getId(), 'os' => $sessionOs->getOs()]);
        $this->assertNotNull($item);        
    }     
}
