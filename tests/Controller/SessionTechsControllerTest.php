<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\SessionTechs;
use App\Repository\SessionTechsRepository;
use App\Entity\Sessions;
use App\Entity\Testees;
use App\Entity\SessionStatuses;
use App\Repository\SessionsRepository;
use App\Repository\TesteesRepository;
use App\Repository\SessionStatusesRepository;
use App\Entity\Technologies;
use App\Repository\TechnologiesRepository;
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
        $this->sessionTechsRepository = $this->entityManager->getRepository(SessionTechs::class);
        $this->sessionsRepository = $this->entityManager->getRepository(Sessions::class);
        $this->testeesRepository = $this->entityManager->getRepository(Testees::class);
        $this->sessionsStatusesRepository = $this->entityManager->getRepository(SessionStatuses::class);
        $this->technologiesRepository = $this->entityManager->getRepository(Technologies::class);
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
    public function testCanAddDummySessionTechBySubmittingForm(): void {
        
        $session = $this->addDummySession();
        $this->assertNotNull($session);
        
        $tech = $this->technologiesRepository->findOneBy([]);
        $this->assertNotNull($tech);
        
        $crawler = $this->client->request('GET', '/session/techs/new');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Save');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['session_techs[session]'] = strval($session->getId());
        $form['session_techs[tech]'] = strval($tech->getId());

        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->sessionTechsRepository->findOneBy(
                ['session' => $session->getId(), 'tech' => $tech->getId()]);
        $this->assertNotNull($item);        
    }       
    
    /**
     * @depends testSessionTechsListIsNotEmpty
     * @param array<SessionTechs> $sessionTechs
     * @return void
     */
    public function testCanEditSessionTechBySubmittingForm($sessionTechs): void {
        
        $session = $this->addDummySession();
        $this->assertNotNull($session);
        
        $sessionTech = $this->sessionTechsRepository->findOneById($sessionTechs[0]);
        $this->assertNotNull($sessionTech);
            
        $crawler = $this->client->request('GET', '/session/techs/'.$sessionTech->getId().'/edit');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Update');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['session_techs[session]'] = strval($session->getId());
        $form['session_techs[tech]'] = strval($sessionTech->getTech()->getId());

        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->sessionTechsRepository->findOneBy(
                ['session' => $session->getId(), 'tech' => $sessionTech->getTech()]);
        $this->assertNotNull($item);        
    }      
}
