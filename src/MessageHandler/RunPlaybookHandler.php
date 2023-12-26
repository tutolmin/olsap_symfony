<?php

namespace App\MessageHandler;

use App\Message\RunPlaybook;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use Doctrine\ORM\EntityManagerInterface;
#use App\Entity\InstanceStatuses;
#use App\Entity\Instances;
#use App\Entity\InstanceTypes;
use App\Entity\Environments;

use Psr\Log\LoggerInterface;

#[AsMessageHandler(fromTransport: 'async', bus: 'awx.bus')]
final class RunPlaybookHandler
{
    // Logger reference
    private $logger;

    // Doctrine EntityManager
    private $entityManager;

    private $message;
    private $environmentRepository;

    public function __construct(
        LoggerInterface $logger, EntityManagerInterface $entityManager)
    {   
        $this->logger = $logger;

        $this->entityManager = $entityManager;
        $this->environmentRepository = $this->entityManager->getRepository( Environments::class);
#        $this->instanceTypeRepository = $this->entityManager->getRepository( InstanceTypes::class);
#        $this->instanceStatusRepository = $this->entityManager->getRepository( InstanceStatuses::class);
#        $this->instanceRepository = $this->entityManager->getRepository( Instances::class);
    }

    public function __invoke(RunPlaybook $message)
    {
        // Get passed optional parameters
        $environment = null;
        if( strlen($message->getEnvironmentId()))
          $environment = $this->environmentRepository->find($message->getEnvironmentId());

        // Switch playbook name to serve
        switch( $message->getName()) {

        // Deploy an environment
        case "deploy":
	  break;



        default:
            $this->logger->debug( "Unknown playbook name: `" . $message->getName() . "`");
          break;
        }
    }
}
