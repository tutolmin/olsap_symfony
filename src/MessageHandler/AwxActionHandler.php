<?php

namespace App\MessageHandler;

use App\Message\AwxAction;
#use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EnvironmentsRepository;
#use App\Entity\InstanceStatuses;
#use App\Entity\Instances;
#use App\Entity\InstanceTypes;
use App\Entity\Environments;
#use App\Service\AwxManager;

use Psr\Log\LoggerInterface;

#[AsMessageHandler(fromTransport: 'async', bus: 'awx.action.bus')]
final class AwxActionHandler
{
    // Logger reference
    private LoggerInterface $logger;

    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

//    private $awxService;
    
//    private $message;
    private EnvironmentsRepository $environmentRepository;

    public function __construct(
        LoggerInterface $logger, EntityManagerInterface $entityManager,
#        AwxManager $manager
            )
    {   
        $this->logger = $logger;

#        $this->awxService = $manager;
        $this->entityManager = $entityManager;
        $this->environmentRepository = $this->entityManager->getRepository( Environments::class);
#        $this->instanceTypeRepository = $this->entityManager->getRepository( InstanceTypes::class);
#        $this->instanceStatusRepository = $this->entityManager->getRepository( InstanceStatuses::class);
#        $this->instanceRepository = $this->entityManager->getRepository( Instances::class);

        $this->logger->debug(__METHOD__);
    }

    public function __invoke(AwxAction $message)
    {
        // Get passed optional parameters
        $environment = null;
        if($message->getEnvironmentId()>0)
        {
          $environment = $this->environmentRepository->find($message->getEnvironmentId());
        }
        
        // Switch playbook name to serve
        switch( $message->getAction()) {

        // Update the inventory
        case "inventory":
	  break;

        // Deploy an environment
        case "deploy":
	  break;

        default:
          $this->logger->debug( "Unknown playbook: `" . $message->getAction() . "`");
          break;
        }
    }
}
