<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Testees;
use App\Repository\TesteesRepository;
use App\Service\SessionManager;

class TesteesManager
{
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private TesteesRepository $testeeRepository;

    /**
     * 
     * @var SessionManager
     */
    private $sessionManager;

    public function __construct( LoggerInterface $logger, EntityManagerInterface $em, 
            SessionManager $sessionManager, 
)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
	$this->sessionManager = $sessionManager;
        $this->testeeRepository = $this->entityManager->getRepository( Testees::class);
    }

    /**
     * 
     * @param Testees $testee
     * @return bool
     */
    public function removeTestee(Testees $testee): bool {

        if ($testee->getSessionsCounter() > 0) {

            foreach ($testee->getSessions() as $session) {
                $this->sessionManager->removeSession($session);
            }
        }

        $this->testeeRepository->remove($testee, true);

        return true;
    }
}

