<?php

namespace App\MessageHandler;

use App\Message\EnvironmentAction;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sessions;
use App\Entity\Tasks;
use App\Service\EnvironmentManager;

#[AsMessageHandler(fromTransport: 'async', bus: 'environment.action.bus')]
final class EnvironmentActionHandler
{
    // Logger reference
    private $logger;
    private $environmentService;

    // Doctrine EntityManager
    private $entityManager;

    // Repositories
    private $taskRepository;
    private $sessionRepository;
    
    public function __construct( LoggerInterface $logger, 
            EntityManagerInterface $entityManager, EnvironmentManager $environmentService)
    {
        $this->logger = $logger;
        $this->environmentService = $environmentService;
        $this->entityManager = $entityManager;
        $this->taskRepository = $this->entityManager->getRepository(Tasks::class);
        $this->sessionRepository = $this->entityManager->getRepository(Sessions::class);

        $this->logger->debug(__METHOD__);
    }

    public function __invoke(EnvironmentAction $message) {
        // Get passed parameters
        $task = null;
        if (strlen($message->getTaskId())) {
            $task = $this->taskRepository->find($message->getTaskId());
        }
        $session = null;
        if (strlen($message->getSessionId())) {
            $session = $this->sessionRepository->find($message->getSessionId());
        }
        // Switch action to serve
        switch ($message->getAction()) {

            // Create the environment
            case "create":

                if (!$task) {
                    $this->logger->error("Task is required for `" . $message->getAction() . "` acrion");
                    break;
                }
                if ($session) {
                    $this->environmentService->createEnvironment($task->getId(), $session->getId(), false);
                } else {
                    $this->environmentService->createEnvironment($task->getId(), null, false);
                }
                break;

            default:
                $this->logger->debug("Unknown command: `" . $message->getAction() . "`");
                break;
        }
    }
}
