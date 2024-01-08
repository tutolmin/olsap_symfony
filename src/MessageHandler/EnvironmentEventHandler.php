<?php

namespace App\MessageHandler;

use App\Message\EnvironmentEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
#use App\Message\RunPlaybook;
#use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\InstanceStatuses;
use App\Entity\Instances;
use App\Entity\InstanceTypes;
use App\Entity\Environments;
use App\Entity\EnvironmentStatuses;
use Psr\Log\LoggerInterface;
use App\Service\LxcManager;
use App\Service\SessionManager;

#[AsMessageHandler(fromTransport: 'async', bus: 'env.event.bus')]
final class EnvironmentEventHandler {

    // Logger reference
    private $logger;
    // Doctrine EntityManager
    private $entityManager;
    private $environmentRepository;
    private $environmentStatusesRepository;
    private $instanceTypeRepository;
    private $instanceStatusRepository;
    private $instanceRepository;
    // Message bus
    private $awxBus;
    private $lxdBus;
    private $lxdService;
    private $session;

    public function __construct(
            LoggerInterface $logger, EntityManagerInterface $entityManager,
            MessageBusInterface $awxBus, MessageBusInterface $lxdBus,
            LxcManager $lxd, SessionManager $session) {
        $this->logger = $logger;
        $this->awxBus = $awxBus;
        $this->lxdBus = $lxdBus;
        $this->lxdService = $lxd;
        $this->session = $session;

        $this->entityManager = $entityManager;
        $this->instanceTypeRepository = $this->entityManager->getRepository(InstanceTypes::class);
        $this->environmentRepository = $this->entityManager->getRepository(Environments::class);
        $this->environmentStatusesRepository = $this->entityManager->getRepository(EnvironmentStatuses::class);
        $this->instanceStatusRepository = $this->entityManager->getRepository(InstanceStatuses::class);
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);
    }

    public function __invoke(EnvironmentEvent $message) {
        // Get passed optional parameters
        $id = null;
        if (strlen($message->getId())) {
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

                $this->logger->debug("Handling instance status change `" . $message->getEvent() . "` for Environment ID`" . $id . "`");

                $env = $this->environmentRepository->findOneById($id);
                if ($env) {
                    $this->lxdService->setInstanceStatus($env->getInstance()->getId(), "Running");

                    $env_status = $this->environmentStatusesRepository->findOneByStatus("Created");
                    $env->setStatus($env_status);
                    // Store item into the DB
                    $this->entityManager->persist($env);
                }
                # TODO: Handle exception
                break;

            default :
                $this->logger->debug("Unknown Environment event: `" . $message->getEvent() . "`");

                break;
        }
    }
}
