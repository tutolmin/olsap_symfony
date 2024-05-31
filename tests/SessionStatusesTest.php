<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\SessionStatuses;
use App\Repository\SessionStatusesRepository;
use App\Service\SessionStatusesManager;

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

    private SessionStatusesManager $sessionStatusesManager;
    
    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->sessionStatusesRepository = $this->entityManager->getRepository(SessionStatuses::class);
        
        $this->sessionStatusesManager = static::getContainer()->get(SessionStatusesManager::class);        
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
     * 
     * @depends testSessionStatusesListIsNotEmpty
     * @param array<SessionStatuses> $session_statuses
     * @return void
     */
    public function testCanRemoveAllSessionStatuses(array $session_statuses): void { 

        foreach ($session_statuses as $s) {
            
            $session_status = $this->sessionStatusesRepository->findOneById($s);
            $this->assertNotNull($session_status);
            $id = $session_status->getId();

            $this->sessionStatusesManager->removeSessionStatus($session_status);
            
            $removed_session_status = $this->sessionStatusesRepository->findOneById($id);
            $this->assertNull($removed_session_status);
        }
    }
}
