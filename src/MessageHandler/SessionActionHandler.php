<?php

namespace App\MessageHandler;

use App\Message\SessionAction;
//use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TasksRepository;
use App\Repository\SessionsRepository;
use App\Repository\EnvironmentsRepository;
use Psr\Log\LoggerInterface;
use App\Entity\Sessions;
use App\Entity\Tasks;
use App\Entity\Environments;
use App\Service\SessionManager;
use App\Service\EnvironmentManager;

#[AsMessageHandler(fromTransport: 'async', bus: 'session.action.bus')]
final class SessionActionHandler
{
    // Logger reference
    private LoggerInterface $logger;

    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

    // SessionManager
    /**
     * 
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var EnvironmentManager
     */
    private $envManager;
            
    // Repositories
    private TasksRepository $taskRepository;
    
    /**
     * 
     * @var SessionsRepository
     */
    private $sessionRepository;
    
    /**
     * 
     * @var EnvironmentsRepository
     */
    private $environmentRepository;

    public function __construct(
        LoggerInterface $logger, EntityManagerInterface $entityManager,
        SessionManager $sessionManager, EnvironmentManager $envManager)
    {   
        $this->logger = $logger;
	$this->sessionManager = $sessionManager;
        $this->envManager = $envManager;
	$this->entityManager = $entityManager;
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
        $this->sessionRepository = $this->entityManager->getRepository( Sessions::class);
        $this->environmentRepository = $this->entityManager->getRepository( Environments::class);
    }
/*
    public function createEnvironment(Tasks $task, Sessions $session)
    {
	$environment = $this->sessionManager->createEnvironment($task,$session);
	$this->logger->debug( "Created environment: " . $environment);

	if($environment) {

	  $result = $this->sessionManager->deployEnvironment($environment);
	  $this->logger->debug( "Environment deployed successfully!");
	} else {

	  $this->logger->debug( "Environment deployment failure!");
	}
    }
*/
    public function __invoke(SessionAction $message): void
    {
        // Get passed parameters
        $task = null;
        if ($message->getTaskId() > 0) {
            $task = $this->taskRepository->find($message->getTaskId());
        }
        $session = null;
        if ($message->getSessionId() > 0) {
            $session = $this->sessionRepository->find($message->getSessionId());
        }
        $environment = null;
        if ($message->getEnvironmentId() > 0) {
            $environment = $this->environmentRepository->find($message->getEnvironmentId());
        }
        // Switch action to serve
        switch( $message->getAction()) {
/*
        // Allocate any available environments
        case "allocateEnvironment":

          // Session has been specified
	  $task = $this->sessionManager->getRandomTask();
          if($session)
	    $task = $this->sessionManager->getNextTask($session);

            $this->logger->debug( "Selected task: " . $task);
	
	    $environment = $this->environmentRepository->findOneDeployed($session);

	    // Environment has been found
	    if($environment) {

	      $environment->setSession($session);

	      // Store item into the DB
	      $this->entityManager->persist($environment);
	      $this->entityManager->flush();
   
	      $this->logger->debug( "Allocated environment: " . $environment);
	
	    // No env to allocate, create it
	    } else {

	      $environment = $this->sessionManager->createEnvironment($task,$session);
	      $this->logger->debug( "Created environment: " . $environment);
	    }

          break;
*/
            // create spare environment for a task
            case "createSpareEnvironment":

                if (!$task) {
                    $this->logger->error("Task is required for spare env creation.");
                    break;
                }

                // TODO: make sure task exists, and there are not enough spare envs for a task
                $environments = $this->environmentRepository->findAllDeployed($task->getId());
                $env_counter = count($environments?$environments:[]);

                $this->logger->debug("Specified task: " . $task . ", spare envs #: " . $env_counter);

                if (!is_numeric($task->getDeploy())) {

                    $this->logger->warning("Task does NOT have deployment template. Skipping deployment!");
                }

                // Only add new envs if there are not enough
                if ($env_counter < $_ENV['APP_SPARE_ENVS']) {
//              && is_numeric($task->getDeploy())) {
//              $task->getDeploy()) {


                    $environment = $this->envManager->createEnvironment($task->getId());
                    #            $this->logger->debug( "Created environment: " . $environment);

                    if ($environment) {

                        $result = $this->envManager->deployEnvironment($environment);
                        #              $this->logger->debug( "Environment deployed successfully!");
                    } else {

                        #              $this->logger->debug( "Environment deployment failure!");
                    }
                }

                break;

            // Binding some orphan instance to an env
            case "createEnvironment":

                // Task has not been specified, get random
                if (!$task) {

                    // Session has been specified
                    if ($session) {
                        $task = $this->sessionManager->getNextTask($session);
                    } else {
                        $task = $this->sessionManager->getRandomTask();
                    }
                }
                $this->logger->debug("Selected task: " . $task);

                if (!is_numeric($task->getDeploy())) {

                    $this->logger->warning("Task does NOT have deployment template. Skipping deployment!");

                    break;
                }

                $environment = $this->envManager->createEnvironment($task->getId(),
                        $session ? $session->getId() : -1);
#            $this->logger->debug( "Created environment: " . $environment);

                if ($environment) {

                    $result = $this->envManager->deployEnvironment($environment);
#              $this->logger->debug( "Environment deployed successfully!");
                } else {

#              $this->logger->debug( "Environment deployment failure!");
                }

                break;

            // Verify the environment
        case "verifyEnvironment":

        if($environment){
            // Verify the environment
	  $this->envManager->verifyEnvironment($environment);
        }
          /*
	  // Allocate new environment instead of verified one
	  $this->sessionManager->allocateEnvironment($environment->getSession());
*/
          break;

        default:
            $this->logger->debug( "Unknown command: `" . $message->getAction() . "`");
          break;
        }


    }
}
