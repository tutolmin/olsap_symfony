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

            // Updated the inventory
            case "inventory": 
                
//                $inventoryResult = $this->awxService->getJobById($id);
//                $this->logger->debug("Inventory update status: " . $inventoryResult->status);
                $this->logger->debug("Inventory update");
                
                break;

            // Playbook execution details
            case "playbook":
                
                $job = $this->awxService->getJobById($id);
                $this->logger->debug("Job status: " . $job->status);

                // Check what playbook was executed? 
                // Update env status accordingly
                // Update verification status if verify.yml
                
                break;

            // Project event
            case "project":

//                $projectResult = $this->awxService->getJobById($id);
//                $this->logger->debug("Current Project status: " . $projectResult->status);
                $this->logger->debug("Project update");

                break;

            default:
                $this->logger->debug("Unknown AWX event: `" . $message->getEvent() . "`");
                break;
        }
    }
}
