<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sessions;
use App\Entity\SessionStatuses;
use App\Repository\SessionsRepository;
use App\Repository\SessionStatusesRepository;
use App\Service\SessionManager;

class SessionStatusesManager {

    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private SessionsRepository $sessionRepository;
    private SessionStatusesRepository $sessionStatusesRepository;
    private SessionManager $sessionManager;
    
    public function __construct(
            LoggerInterface $logger, EntityManagerInterface $em,
            SessionManager $sessionManager
    ) {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
        $this->sessionRepository = $this->entityManager->getRepository(Sessions::class);
        $this->sessionStatusesRepository = $this->entityManager->getRepository(SessionStatuses::class);
        $this->sessionManager = $sessionManager;
    }

    /**
     * 
     * @param SessionStatuses $sessionStatus
     */
    public function removeSessionStatus(SessionStatuses $sessionStatus): void {

        $this->logger->debug(__METHOD__);

        $sessions = $this->sessionRepository->findBy(['status' => $sessionStatus->getId()]);

        if ($sessions) {
            foreach ($sessions as $session) {
                $this->sessionManager->removeSession($session);
            }
        }

        $this->sessionStatusesRepository->remove($sessionStatus, true);
    }
}
