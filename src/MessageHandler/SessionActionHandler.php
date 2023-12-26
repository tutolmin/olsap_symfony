<?php

namespace App\MessageHandler;

use App\Message\SessionAction;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use App\Entity\Sessions;
use App\Entity\Tasks;
use App\Entity\Environments;
use App\Service\SessionManager;

#[AsMessageHandler(fromTransport: 'async', bus: 'session.bus')]
final class SessionActionHandler
{
    // Logger reference
    private $logger;

    // Doctrine EntityManager
    private $entityManager;

    // SessionManager
    private $sessionManager;

    // Repositories
    private $taskRepository;
    private $sessionRepository;
    private $environmentRepository;

    public function __construct(
        LoggerInterface $logger, EntityManagerInterface $entityManager,
        SessionManager $sessionManager)
    {   
        $this->logger = $logger;
	$this->sessionManager = $sessionManager;
	$this->entityManager = $entityManager;
        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
        $this->sessionRepository = $this->entityManager->getRepository( Sessions::class);
        $this->environmentRepository = $this->entityManager->getRepository( Environments::class);
    }

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

    public function __invoke(SessionAction $message)
    {
        // Get passed parameters
        $task = null;
        if( strlen($message->getTaskId()))
          $task = $this->taskRepository->find($message->getTaskId());

        $session = null;
        if( strlen($message->getSessionId()))
          $session = $this->sessionRepository->find($message->getSessionId());

        $environment = null;
        if( strlen($message->getEnvironmentId()))
          $environment = $this->environmentRepository->find($message->getEnvironmentId());

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

	  // TODO: make sure task exists, and there are not enough spare envs for a task
          $environments = $this->environmentRepository->findAllDeployed($task->getId());

	  $this->logger->debug( "Specified task: " . $task . ", spare envs #: " . count($environments));
	
	  if(!is_numeric($task->getDeploy())) {

	    $this->logger->warning( "Task does NOT have deployment template. Skipping deployment!");

	    break;
    	  }

	  // Only add new envs if there are not enough
	  if(count($environments) < $_ENV['APP_SPARE_ENVS'] &&
		is_numeric($task->getDeploy())) {

	    $environment = $this->sessionManager->createEnvironment($task);
  #            $this->logger->debug( "Created environment: " . $environment);

	    if($environment) {

	      $result = $this->sessionManager->deployEnvironment($environment);
  #              $this->logger->debug( "Environment deployed successfully!");
	    } else {

  #              $this->logger->debug( "Environment deployment failure!");
	    }
	  }

          break;

        // Binding some orphan instance to an env
        case "createEnvironment":

	  // Task has not been specified, get random
	  if(!$task)

	    // Session has been specified
	    if($session)

	      $task = $this->sessionManager->getNextTask($session);

	    else

	      $task = $this->sessionManager->getRandomTask();

	  $this->logger->debug( "Selected task: " . $task);
	
	  if(!is_numeric($task->getDeploy())) {

	    $this->logger->warning( "Task does NOT have deployment template. Skipping deployment!");

	    break;
    	  }

	  $environment = $this->sessionManager->createEnvironment($task,$session);
#            $this->logger->debug( "Created environment: " . $environment);

	  if($environment) {

	    $result = $this->sessionManager->deployEnvironment($environment);
#              $this->logger->debug( "Environment deployed successfully!");
	  } else {

#              $this->logger->debug( "Environment deployment failure!");
	  }

          break;

        // Verify the environment
        case "verifyEnvironment":

	  // Verify the environment
	  $result = $this->sessionManager->verifyEnvironment($environment);

	  // Allocate new environment instead of verified one
	  $this->sessionManager->allocateEnvironment($environment->getSession());

          break;

        default:
            $this->logger->debug( "Unknown command: `" . $message->getAction() . "`");
          break;
        }


    }
}
