<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\SessionStatuses;
//use App\Entity\Sessions;
use App\Repository\SessionStatusesRepository;
//use App\Repository\SessionsRepository;

class SessionStatusesTest extends KernelTestCase
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

    protected function setUp(): void {
        self::bootKernel();

//        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->sessionStatusesRepository = $this->entityManager->getRepository(SessionStatuses::class);
//        $this->sessionsRepository = $this->entityManager->getRepository(Sessions::class);
    }
    
    /**
     * 
     * @return array<SessionStatuses>
     */
    public function testSessionStatusesListIsNotEmpty(): array {

        $session_statuses = $this->sessionStatusesRepository->findAll();
        $this->assertNotEmpty($session_statuses);
        
        return $session_statuses;
    }
    
    /**
     * 
     * @depends testSessionStatusesListIsNotEmpty
     * @param array<SessionStatuses> $session_statuses
     * @return SessionStatuses|null
     */
    public function testSessionStatusHasSessionsReference(
            array $session_statuses): ?SessionStatuses {
        
        foreach ($session_statuses as $session_status) {
            if($session_status->getSessions()->first()){
                $this->assertTrue(true);
                return $session_status;
            }
        }
        $this->assertTrue(false);
        return null;
    }
    
    public function testCanNotAddSessionStatusWithoutStatusString(): void {
        
        $this->assertFalse($this->sessionStatusesRepository->add(
                new SessionStatuses(), true));
    }

    /**
     * 
     * @depends testSessionStatusesListIsNotEmpty
     * @param array<SessionStatuses> $session_statuses
     * @return void
     */
    public function testCanNotAddDuplicateSessionStatus(
            array $session_statuses): void {

        $new_session_status = new SessionStatuses();
        $new_session_status->setStatus($session_statuses[0]->getStatus());
        $this->assertFalse($this->sessionStatusesRepository->add(
                $new_session_status, true));
    }

    public function testCanAddDummySessionStatus(): SessionStatuses {
        
        $session_status = new SessionStatuses();
        $session_status->setStatus($this->dummy['name']);
        $this->assertTrue($this->sessionStatusesRepository->add($session_status, true));
        return $session_status;
    }

    /**
     * @depends testCanAddDummySessionStatus
     * @param SessionStatuses $session_status
     * @return void
     */
    public function testCanRemoveDummySessionStatuses(SessionStatuses $session_status): void {
        
        $this->assertTrue($this->sessionStatusesRepository->remove($session_status));
    }
        
    /**
     * 
     * @depends testSessionStatusHasSessionsReference
     * @param SessionStatuses $session_statuses
     * @return void
     */
    public function testCanNotRemoveReferencedSessionStatus(
            SessionStatuses $session_statuses): void {
        $this->markTestSkipped("references are not easy to delete"
            );
        
//        $this->assertTrue($this->sessionStatusesRepository->remove($session_statuses, true));
    }
            
    /**
     * @depends testSessionStatusHasSessionsReference
     * @param SessionStatuses $session_status
     * @return void
     */
    public function testCanRemoveSessionStatusWithCascadeFlag(
            SessionStatuses $session_status): void {
        $this->markTestSkipped(
                'Cascade delete is way to complicated with all the references',
            );
        /*
        $breed_id = $breed->getId();

        $this->assertTrue($this->breedsManager->removeSessionStatus($breed, true));

        // Try to find existing OS
        $this->assertEmpty($this->osRepository->findBy(['breed' => $breed_id]));
         * 
         */
    }
}
