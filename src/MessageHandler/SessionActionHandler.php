<?php

namespace App\MessageHandler;

use App\Message\SessionAction;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use App\Entity\Sessions;
use App\Entity\Tasks;
use App\Entity\Environments;
use App\Service\SessionManager;

final class SessionActionHandler implements MessageHandlerInterface
{
    // Logger reference
    private $logger;

    // Doctrine EntityManager
    private $entityManager;

    // SessionManager
    private $sessionManager;

    // Repositories
    private $sessionRepository;
    private $environmentRepository;

    public function __construct(
        LoggerInterface $logger, EntityManagerInterface $entityManager,
        SessionManager $sessionManager)
    {   
        $this->logger = $logger;
	$this->sessionManager = $sessionManager;
	$this->entityManager = $entityManager;
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
        // Binding some orphan instance to an env
        case "createEnvironment":

          // Session has been specified
	  $task = $this->sessionManager->getRandomTask();
          if($session)
	    $task = $this->sessionManager->getNextTask($session);

            $this->logger->debug( "Selected task: " . $task);

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

	  $result = $this->sessionManager->verifyEnvironment($environment);

	  $this->sessionManager->allocateEnvironment($environment->getSession());

          break;

        default:
            $this->logger->debug( "Unknown command: `" . $message->getAction() . "`");
          break;
        }


    }
}
