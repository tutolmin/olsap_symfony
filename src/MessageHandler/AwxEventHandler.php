<?php

namespace App\MessageHandler;

use App\Message\AwxEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;
use App\Service\AwxManager;

#[AsMessageHandler(fromTransport: 'async', bus: 'awx.event.bus')]
final class AwxEventHandler
{
    // Logger reference
    private $logger;
    private $awxService;

    public function __construct(
        LoggerInterface $logger, AwxManager $manager)
    {   
        $this->logger = $logger;

        $this->awxService = $manager;

        $this->logger->debug(__METHOD__);
    }
    
    public function __invoke(AwxEvent $message) {
        
        // Get passed optional parameters
        $id = null;
        if (strlen($message->getId())) {
            $id = $message->getId();
            $this->logger->debug("Provided ID: " . $id);
        }
        
        // Switch event to handle
        switch ($message->getEvent()) {

            // Update the inventory
            case "inventory":
                break;

            // Playbook execution
            case "playbook":
                
                $job = $this->awxService->getJobById($id);
                $this->logger->debug("Job status: " . $job->status);

                break;

            // Project event
            case "project":

//                $projectResult = $this->awxService->getById($id);
//                $this->logger->debug("Current Project status: " . $projectResult->status);

                break;

            default:
                $this->logger->debug("Unknown AWX event: `" . $message->getEvent() . "`");
                break;
        }
    }
}
