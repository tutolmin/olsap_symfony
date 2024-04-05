<?php

namespace App\MessageHandler;

use App\Message\EnvironmentAction;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InstancesRepository;
use App\Repository\TasksRepository;
use App\Entity\Sessions;
use App\Entity\Instances;
use App\Entity\Environments;
use App\Entity\Tasks;
use App\Service\EnvironmentManager;

#[AsMessageHandler(fromTransport: 'async', bus: 'environment.action.bus')]
final class EnvironmentActionHandler
{
    // Logger reference
    private LoggerInterface $logger;
    private $environmentService;

    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    // Repositories
    private TasksRepository $taskRepository;
    private $sessionRepository;
    private $environmentRepository;
    private InstancesRepository $instanceRepository;
    
    public function __construct( LoggerInterface $logger, 
            EntityManagerInterface $entityManager, EnvironmentManager $environmentService)
    {
        $this->logger = $logger;
        $this->environmentService = $environmentService;
        $this->entityManager = $entityManager;
        $this->taskRepository = $this->entityManager->getRepository(Tasks::class);
        $this->sessionRepository = $this->entityManager->getRepository(Sessions::class);
        $this->environmentRepository = $this->entityManager->getRepository(Environments::class);
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);

        $this->logger->debug(__METHOD__);
    }

    public function __invoke(EnvironmentAction $message) {
        // Get passed parameters
        $task = null;
        if ($message->getTaskId()>0) {
            $task = $this->taskRepository->find($message->getTaskId());
        }
        $environment = null;
        if ($message->getEnvId()>0) {
            $environment = $this->environmentRepository->find($message->getEnvId());
        }
        $instance = null;
        if (strlen($message->getInstanceName())) {
            $instance = $this->instanceRepository->findByName($message->getInstanceName());
        }                
        $session = null;
        if ($message->getSessionId()>0) {
            $session = $this->sessionRepository->find($message->getSessionId());
        }
        // Switch action to serve
        switch ($message->getAction()) {

            // Create the environment
            case "create":

                if (!$task) {
                    $this->logger->error("Task is required for `" . $message->getAction() . "` action");
                    break;
                }
                if ($session) {
                    $this->environmentService->createEnvironment($task->getId(), $session->getId(), false);
                } else {
                    $this->environmentService->createEnvironment($task->getId(), -1, false);
                }
                break;

            // Bind the instance to the environment
            case "bind":

                if (!$environment || !$instance) {
                    $this->logger->error("Env and Instance are required for `" . $message->getAction() . "` action");
                    break;
                }
                
                $this->environmentService->bindInstance($environment, $instance);
                
                break;
                
            default:
                $this->logger->debug("Unknown command: `" . $message->getAction() . "`");
                break;
        }
    }
}
