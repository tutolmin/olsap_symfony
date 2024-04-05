<?php

namespace App\MessageHandler;

use App\Message\EnvironmentEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
#use App\Message\RunPlaybook;
#use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
//use Symfony\Component\Messenger\MessageBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EnvironmentsRepository;
//use App\Entity\InstanceStatuses;
//use App\Entity\Instances;
//use App\Entity\InstanceTypes;
use App\Entity\Environments;
//use App\Entity\EnvironmentStatuses;
use Psr\Log\LoggerInterface;
//use App\Service\LxcManager;
//use App\Service\SessionManager;

#[AsMessageHandler(fromTransport: 'async', bus: 'environment.event.bus')]
final class EnvironmentEventHandler {

    // Logger reference
    private LoggerInterface $logger;
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;
    private EnvironmentsRepository $environmentRepository;
//    private $environmentStatusesRepository;
//    private $instanceTypeRepository;
//    private $instanceStatusRepository;
//    private InstancesRepository $instanceRepository;
    // Message bus
//    private $awxBus;
//    private $lxcOperationBus;
//    private LxcManager $lxcService;
//    private $session;

    public function __construct(
            LoggerInterface $logger, EntityManagerInterface $entityManager,
//            MessageBusInterface $awxBus, MessageBusInterface $lxcOperationBus,
//            LxcManager $lxcService, SessionManager $session
            ) {
        $this->logger = $logger;
//        $this->awxBus = $awxBus;
//        $this->lxcOperationBus = $lxcOperationBus;
//        $this->lxcService = $lxcService;
//        $this->session = $session;

        $this->entityManager = $entityManager;
//        $this->instanceTypeRepository = $this->entityManager->getRepository(InstanceTypes::class);
        $this->environmentRepository = $this->entityManager->getRepository(Environments::class);
//        $this->environmentStatusesRepository = $this->entityManager->getRepository(EnvironmentStatuses::class);
//        $this->instanceStatusRepository = $this->entityManager->getRepository(InstanceStatuses::class);
//        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);

        $this->logger->debug(__METHOD__);
    }

    public function __invoke(EnvironmentEvent $message) {
        // Get passed optional parameters
        $id = null;
        if (strlen($message->getId())>0) {
            $id = $message->getId();
        }

        // Select event to serve
        switch ($message->getEvent()) {

            // Environment just created
            case "created":

                // REQUIRED: id
                if (!$id) {
                    $this->logger->error("Env ID is required for `" . $message->getEvent() . "` event");
                    break;
                }

                $this->logger->debug("Handling environment event `" . 
                        $message->getEvent() . "` for Environment ID " . $id);

                $env = $this->environmentRepository->findOneById($id);
                if ($env) {
                    
/*                   
                    $this->lxcService->setInstanceStatus($env->getInstance()->getId(), "Running");

                    $env_status = $this->environmentStatusesRepository->findOneByStatus("Created");
                    $env->setStatus($env_status);
                    // Store item into the DB
                    $this->entityManager->flush();
                */                    
                }
                
                # TODO: Handle exception
                break;

            default :
                $this->logger->debug("Unknown Environment event: `" . $message->getEvent() . "`");

                break;
        }
    }
}
