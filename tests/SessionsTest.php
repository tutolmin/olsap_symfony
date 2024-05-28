<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sessions;
use App\Entity\Testees;
use App\Entity\SessionStatuses;
use App\Repository\SessionsRepository;
use App\Repository\TesteesRepository;
use App\Repository\SessionStatusesRepository;
use App\Service\SessionManager;

class SessionsTest extends KernelTestCase
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
     * @var SessionManager
     */
    private $sessionManager;
    
    protected function setUp(): void {
        self::bootKernel();

//        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->sessionsRepository = $this->entityManager->getRepository(Sessions::class);
        $this->testeesRepository = $this->entityManager->getRepository(Testees::class);
        $this->sessionsStatusesRepository = $this->entityManager->getRepository(SessionStatuses::class);

        $this->sessionManager = static::getContainer()->get(SessionManager::class);        
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

    public function testCanNotAddSessionWithoutMandatoryFields(): void {
        
        $this->assertFalse($this->sessionsRepository->add(new Sessions(), true));
    }
    
    /**
     * 
     * @return Sessions
     */
    public function testCanAddDummySession(): Sessions {

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
     * @depends testSessionsListIsNotEmpty
     * @param array<Sessions> $sessions
     * @return void
     */
    public function testCanRemoveAllSessions(array $sessions): void { 

        foreach ($sessions as $s) {
            
            $session = $this->sessionsRepository->findOneById($s);
            $this->assertNotNull($session);
            $id = $session->getId();

            $this->sessionManager->removeSession($session);
            
            $removed_session = $this->sessionsRepository->findOneById($id);
            $this->assertNull($removed_session);
        }
    }

    /**
     * @depends testSessionsListIsNotEmpty
     * @param array<Sessions> $sessions
     * @return void
     */
    public function testCanNotAddDuplicateSession( array $sessions): void {
                
        $existing_record = $sessions[0];

        $sessionStatus = $this->sessionsStatusesRepository->findOneById($existing_record->getStatus()->getId());
        $this->assertNotNull($sessionStatus);
              
        $testee = $this->testeesRepository->findOneById($existing_record->getTestee()->getId());
        $this->assertNotNull($testee);
      
        $session = new Sessions();
        $session->setHash($existing_record->getHash());
        $session->setStatus($sessionStatus);
        $session->setTestee($testee);
        $session->setCreatedAt(new \DateTimeImmutable('now'));
        
        $this->assertFalse($this->sessionsRepository->add($session, true));
    }
}
