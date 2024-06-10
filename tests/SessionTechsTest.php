<?php

namespace App\Tests;

use App\Repository\SessionTechsRepository;
use App\Repository\SessionsRepository;
use App\Repository\TechnologiesRepository;
use App\Entity\Sessions;
use App\Entity\SessionStatuses;
use App\Entity\SessionTechs;
use App\Entity\Testees;
use App\Repository\SessionStatusesRepository;
use App\Repository\TesteesRepository;
use App\Entity\Technologies;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SessionTechsTest extends KernelTestCase
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
    private $dummy = array('name'=>'Dummy', 'hash'=>'dummy');
    
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
     * @var SessionTechsRepository
     */
    private $sessionTechsRepository;

    /**
     * 
     * @var TechnologiesRepository
     */
    private $techsRepository;

    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->sessionsRepository = $this->entityManager->getRepository(Sessions::class);
        $this->sessionTechsRepository = $this->entityManager->getRepository(SessionTechs::class);
        $this->techsRepository = $this->entityManager->getRepository(Technologies::class);
        $this->testeesRepository = $this->entityManager->getRepository(Testees::class);
        $this->sessionsStatusesRepository = $this->entityManager->getRepository(SessionStatuses::class);
    }

    /**
     * 
     * @return array<SessionTechs>
     */
    public function testSessionTechsListIsNotEmpty(): array {

        $task_techs = $this->sessionTechsRepository->findAll();
        $this->assertNotEmpty($task_techs);
        return $task_techs;
    }

    /**
     * @depends testSessionTechsListIsNotEmpty
     * @param array<SessionTechs> $session_techs
     * @return void
     */
    public function testCanNotAddSessionTechWithoutSession(array $session_techs): void {

        $st = $session_techs[0];

        $session = $this->sessionsRepository->findOneById($st->getSession()->getId());
        $this->assertNotNull($session);

        $new_st = new SessionTechs();
        $new_st->setSession($session);

        $this->assertFalse($this->sessionTechsRepository->add($new_st, true));
    }

    /**
     * @depends testSessionTechsListIsNotEmpty
     * @param array<SessionTechs> $session_techs
     * @return void
     */
    public function testCanNotAddSessionTechWithoutTechnology(array $session_techs): void {

        $st = $session_techs[0];

        $technology = $this->techsRepository->findOneById($st->getTech()->getId());
        $this->assertNotNull($technology);

        $new_st = new SessionTechs();
        $new_st->setTech($technology);

        $this->assertFalse($this->sessionTechsRepository->add($new_st, true));
    }
    
    /**
     * @depends testSessionTechsListIsNotEmpty
     * @param array<SessionTechs> $session_techs
     * @return void
     */
    public function testCanNotAddDuplicateSessionTechs( array $session_techs): void {

        $st = $session_techs[0];

        $session = $this->sessionsRepository->findOneById($st->getSession()->getId());
        $this->assertNotNull($session);
        $technology = $this->techsRepository->findOneById($st->getTech()->getId());
        $this->assertNotNull($technology);
        
        $new_st = new SessionTechs();
        $new_st->setSession($session);
        $new_st->setTech($technology);

        $this->assertFalse($this->sessionTechsRepository->add($new_st, true));
    }
    
    /**
     * @depends testSessionTechsListIsNotEmpty
     * @return void
     */
    public function testCanRemoveAllSessionTechs(): void {
    
        $this->sessionTechsRepository->deleteAll();
        
        $session_techs = $this->sessionTechsRepository->findAll();
        $this->assertEmpty($session_techs);        
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
     * @depends testSessionTechsListIsNotEmpty
     * @param array<SessionTechs> $session_techs
     * @return SessionTechs
     */
    public function testCanAddDummySessionTech(array $session_techs): SessionTechs {

        $session = $this->addDummySession();
        $st = $session_techs[0];
        $technology = $this->techsRepository->findOneById($st->getTech()->getId());
        $this->assertNotNull($technology);
        
        $sessionTech = new SessionTechs();
        $sessionTech->setSession($session);
        $sessionTech->setTech($technology);
        $this->assertTrue($this->sessionTechsRepository->add($sessionTech, true));

        return $sessionTech;
    }    
}
