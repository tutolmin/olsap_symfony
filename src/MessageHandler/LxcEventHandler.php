<?php

namespace App\MessageHandler;

use App\Message\LxcEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
#use App\Message\RunPlaybook;
#use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
//use Symfony\Component\Messenger\MessageBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InstancesRepository;
//use App\Entity\InstanceStatuses;
use App\Entity\Instances;
//use App\Entity\InstanceTypes;
//use App\Entity\Environments;
use Psr\Log\LoggerInterface;
use App\Service\LxcManager;

#[AsMessageHandler(fromTransport: 'async', bus: 'lxc.event.bus')]
final class LxcEventHandler {

    // Logger reference
    private LoggerInterface $logger;
    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;
//    private $environmentRepository;
//    private $instanceTypeRepository;
//    private $instanceStatusRepository;
    private InstancesRepository $instanceRepository;
    // Message bus
//    private $awxBus;
    private LxcManager $lxcService;

    public function __construct(
            LoggerInterface $logger, EntityManagerInterface $entityManager,
//            MessageBusInterface $awxBus, 
            LxcManager $lxcService) {
        $this->logger = $logger;
//        $this->awxBus = $awxBus;
        $this->lxcService = $lxcService;

        $this->entityManager = $entityManager;
//        $this->instanceTypeRepository = $this->entityManager->getRepository(InstanceTypes::class);
//        $this->environmentRepository = $this->entityManager->getRepository(Environments::class);
//        $this->instanceStatusRepository = $this->entityManager->getRepository(InstanceStatuses::class);
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);
    }

    public function __invoke(LxcEvent $message): void {

        // Get passed optional parameters
        $name = null;
        if (strlen($message->getName())) {
            $name = $message->getName();
        }

        // Select event to serve
        switch ($message->getEvent()) {

            // Object started
            case "created":

                // REQUIRED: name
                if (!$name) {
                    $this->logger->error("Name is required for `" . $message->getEvent() . "` LXD event");
                    break;
                }

                $this->logger->info("Handling object event `" . $message->getEvent() . "`: `" . $name . "`");

                // Update inventory
//              $this->awxActionBus->dispatch(new AwxAction(["action" => "updateInventory"]));

                # TODO: Handle exception
                break;

            // Object started
            case "started":

                // REQUIRED: name
                if (!$name) {
                    $this->logger->error("Name is required for `" . $message->getEvent() . "` LXD event");
                    break;
                }

                $this->logger->info("Handling object status change `" . $message->getEvent() . "`: `" . $name . "`");

                $instance = $this->instanceRepository->findOneByName($name);
                if ($instance) {
                    $this->lxcService->setInstanceStatus($instance->getName(), "Started");
                }
                # TODO: Handle exception
                break;

            // Instance stopped
            case "stopped":

                // REQUIRED: name
                if (!$name) {
                    $this->logger->error("Name is required for `" . $message->getEvent() . "` LXD event");
                    break;
                }

                $this->logger->info("Handling object status change `" . $message->getEvent() . "`: `" . $name . "`");

                $instance = $this->instanceRepository->findOneByName($name);
                if ($instance) {
                    $this->lxcService->setInstanceStatus($instance->getName(), "Stopped");
                }
                # TODO: Handle exception
                break;

            // Object deleted
            case "deleted":

                // REQUIRED: name
                if (!$name) {
                    $this->logger->error("Name is required for `" . $message->getEvent() . "` LXD event");
                    break;
                }

                $this->logger->info("Handling object event` " . $message->getEvent() . "`: `" . $name . "`");

                // Update inventory
//              $this->awxActionBus->dispatch(new AwxAction(["action" => "updateInventory"]));

                # TODO: Handle exception
                break;
                
            default :
                $this->logger->error("Unknown LXC event: `" . $message->getEvent() . "`");

                break;
        }
    }
}
