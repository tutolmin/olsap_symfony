<?php

namespace App\MessageHandler;

use App\Message\LxcEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
#use App\Message\RunPlaybook;
#use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\InstanceStatuses;
use App\Entity\Instances;
use App\Entity\InstanceTypes;
use App\Entity\Environments;
use Psr\Log\LoggerInterface;
use App\Service\LxcManager;
use App\Service\SessionManager;

#[AsMessageHandler(fromTransport: 'async', bus: 'lxd.event.bus')]
final class LxcEventHandler {

    // Logger reference
    private $logger;
    // Doctrine EntityManager
    private $entityManager;
    private $environmentRepository;
    private $instanceTypeRepository;
    private $instanceStatusRepository;
    private $instanceRepository;
    // Message bus
    private $awxBus;
    private $lxdBus;
    private $lxd;
    private $session;

    public function __construct(
            LoggerInterface $logger, EntityManagerInterface $entityManager,
            MessageBusInterface $awxBus, MessageBusInterface $lxdBus, 
            LxcManager $lxd, SessionManager $session) {
        $this->logger = $logger;
        $this->awxBus = $awxBus;
        $this->lxdBus = $lxdBus;
        $this->lxd = $lxd;        
        $this->session = $session;

        $this->entityManager = $entityManager;
        $this->instanceTypeRepository = $this->entityManager->getRepository(InstanceTypes::class);
        $this->environmentRepository = $this->entityManager->getRepository(Environments::class);
        $this->instanceStatusRepository = $this->entityManager->getRepository(InstanceStatuses::class);
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);
    }

    public function __invoke(LxcEvent $message) {

        // Get passed optional parameters
        $name = null;
        if (strlen($message->getName())) {
            $name = $message->getName();
        }

        // Select event to serve
        switch ($message->getEvent()) {

            // Instance started
            case "created":

                // REQUIRED: name
                if (!$name) {
                    $this->logger->error("Name is required for `" . $message->getEvent() . "` LXD event");
                    break;
                }

                $this->logger->debug("Handling instance status change `" . $message->getEvent() . "`: `" . $name . "`");

                $instance = $this->instanceRepository->findOneByName($name);
                if ($instance) {
                    $this->session->setInstanceStatus($instance, "Started");
                }
                # TODO: Handle exception
                break;

            // Instance started
            case "started":

                // REQUIRED: name
                if (!$name) {
                    $this->logger->error("Name is required for `" . $message->getEvent() . "` LXD event");
                    break;
                }

                $this->logger->debug("Handling instance status change `" . $message->getEvent() . "`: `" . $name . "`");

                $instance = $this->instanceRepository->findOneByName($name);
                if ($instance) {
                    $this->session->setInstanceStatus($instance, "Started");
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

                $this->logger->debug("Handling instance status change `" . $message->getEvent() . "`: `" . $name . "`");

                $instance = $this->instanceRepository->findOneByName($name);
                if ($instance) {
                    $this->session->setInstanceStatus($instance, "Stopped");
                }
                # TODO: Handle exception
                break;

            default :
                $this->logger->debug("Unknown LXC event: `" . $message->getEvent() . "`");

                break;
        }
    }
}
