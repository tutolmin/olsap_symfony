<?php

namespace App\MessageHandler;

use App\Message\LxcOperation;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Repository\EnvironmentsRepository;
use App\Repository\InstanceTypesRepository;

use Psr\Log\LoggerInterface;

#[AsMessageHandler]
final class LxcOperationHandler implements MessageHandlerInterface
{
    // Logger reference
    private $logger;

    private $message;
    private $environmentRepository;
    private $instanceTypeRepository;

    public function __construct(EnvironmentsRepository $environmentRepository,
	InstanceTypesRepository $instanceTypeRepository, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->environmentRepository = $environmentRepository;
        $this->instanceTypeRepository = $instanceTypeRepository;
    }

    public function __invoke(LxcOperation $message)
    {
	$environment = "";
	if( strlen($message->getEnvironmentId()))
          $environment = $this->environmentRepository->find($message->getEnvironmentId());

        $instance_type = $this->instanceTypeRepository->find($message->getInstanceTypeId());

	switch( $message->getCommand()) {
	case "create":
            $this->logger->debug( "Creating LXC instance of type id: `" . $message->getInstanceTypeId() . "`");
	  break;
	default:
            $this->logger->debug( "Unknown command: `" . $message->getCommand() . "`");
	  break;
	}

        // do something with your message
    }
}
