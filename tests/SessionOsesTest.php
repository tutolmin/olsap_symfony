<?php

namespace App\Tests;

use App\Repository\SessionOsesRepository;
use App\Repository\SessionsRepository;
use App\Repository\OperatingSystemsRepository;
use App\Entity\Sessions;
use App\Entity\SessionOses;
use App\Entity\OperatingSystems;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SessionOsesTest extends KernelTestCase
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
     * @var SessionOsesRepository
     */
    private $soRepository;

    /**
     * 
     * @var OperatingSystemsRepository
     */
    private $osRepository;

    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->sessionsRepository = $this->entityManager->getRepository(Sessions::class);
        $this->soRepository = $this->entityManager->getRepository(SessionOses::class);
        $this->osRepository = $this->entityManager->getRepository(OperatingSystems::class);
    }

    /**
     * 
     * @return array<SessionOses>
     */
    public function testSessionOsesListIsNotEmpty(): array {

        $session_oses = $this->soRepository->findAll();
        $this->assertNotEmpty($session_oses);
        return $session_oses;
    }

    /**
     * @depends testSessionOsesListIsNotEmpty
     * @param array<SessionOses> $session_oses
     * @return void
     */
    public function testCanNotAddSessionOsWithoutSession(array $session_oses): void {

        $so = $session_oses[0];

        $session = $this->sessionsRepository->findOneById($so->getSession()->getId());
        $this->assertNotNull($session);

        $new_so = new SessionOses();
        $new_so->setSession($session);

        $this->assertFalse($this->soRepository->add($new_so, true));
    }

    /**
     * @depends testSessionOsesListIsNotEmpty
     * @param array<SessionOses> $session_oses
     * @return void
     */
    public function testCanNotAddSessionOsWithoutOperatingSystem(array $session_oses): void {

        $so = $session_oses[0];

        $os = $this->osRepository->findOneById($so->getOs()->getId());
        $this->assertNotNull($os);

        $new_so = new SessionOses();
        $new_so->setOs($os);

        $this->assertFalse($this->soRepository->add($new_so, true));
    }
    
    /**
     * @depends testSessionOsesListIsNotEmpty
     * @param array<SessionOses> $session_oses
     * @return void
     */
    public function testCanNotAddDuplicateSessionOses( array $session_oses): void {

        $so = $session_oses[0];

        $session = $this->sessionsRepository->findOneById($so->getSession()->getId());
        $this->assertNotNull($session);
        $os = $this->osRepository->findOneById($so->getOs()->getId());
        $this->assertNotNull($os);
        
        $new_so = new SessionOses();
        $new_so->setSession($session);
        $new_so->setOs($os);

        $this->assertFalse($this->soRepository->add($new_so, true));
    }

    /**
     * @depends testSessionOsesListIsNotEmpty
     * @return void
     */
    public function testCanRemoveAllSessionOses(): void {
    
        $this->soRepository->deleteAll();
        
        $session_oses = $this->soRepository->findAll();
        $this->assertEmpty($session_oses);        
    }
}
