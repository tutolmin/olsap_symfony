<?php

namespace App\Tests;

use App\Repository\SessionTechsRepository;
use App\Repository\SessionsRepository;
use App\Repository\TechnologiesRepository;
use App\Entity\Sessions;
use App\Entity\SessionTechs;
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
     * @var SessionsRepository
     */
    private $sessionsRepository;

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
     * @param array<SessionTechs> $session_techs
     * @return void
     */
    public function testCanRemoveRandomSessionTech( array $session_techs): void {

        $st = $this->sessionTechsRepository->findOneById($session_techs[0]->getId());
        $this->assertNotNull($st);
        $id = $st->getId();
    
        $this->sessionTechsRepository->remove($st, true);
        
        $removed_st = $this->sessionTechsRepository->findOneById($id);
        $this->assertNull($removed_st);
    }    
}
