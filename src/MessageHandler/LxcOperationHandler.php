<?php

namespace App\MessageHandler;

use App\Message\LxcOperation;
#use App\Message\LxcEvent;
use App\Message\RunPlaybook;
#use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\InstanceStatuses;
use App\Entity\Instances;
use App\Entity\InstanceTypes;
use App\Entity\Environments;

use Psr\Log\LoggerInterface;

use App\Service\LxcManager;

/*
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;
*/
#[AsMessageHandler(fromTransport: 'async', bus: 'lxd.operation.bus')]
final class LxcOperationHandler
{
    // Logger reference
    private $logger;

    // Doctrine EntityManager
    private $entityManager;

//    private $message;
    private $environmentRepository;
    private $instanceTypeRepository;
    private $instanceStatusRepository;
    private $instanceRepository;

    // Message bus
    private $awxBus;
    private $lxdEventBus;
    private $lxdOperationBus;

    private $lxd;

    public function __construct(
	LoggerInterface $logger, EntityManagerInterface $entityManager,
	MessageBusInterface $awxBus, MessageBusInterface $lxdEventBus, 
        MessageBusInterface $lxdOperationBus, LxcManager $lxd)
    {
        $this->logger = $logger;
        $this->awxBus = $awxBus;
        $this->lxdEventBus = $lxdEventBus;
        $this->lxdOperationBus = $lxdOperationBus;
	$this->lxd = $lxd;

        $this->entityManager = $entityManager;
        $this->instanceTypeRepository = $this->entityManager->getRepository( InstanceTypes::class);
        $this->environmentRepository = $this->entityManager->getRepository( Environments::class);
        $this->instanceStatusRepository = $this->entityManager->getRepository( InstanceStatuses::class);
        $this->instanceRepository = $this->entityManager->getRepository( Instances::class);
/*
        $config = [
            'verify' => false,
            'cert' => [
                $_ENV["PROJECT_DIR"].'/client.pem',
                ''
            ]
        ];
        $guzzle = new GuzzleClient($config);
        $adapter = new GuzzleAdapter($guzzle);
        $this->lxd = new \Opensaucesystems\Lxd\Client($adapter);
        $this->lxd->setUrl($_ENV['LXD_URL']);
*/
    }

    public function __invoke(LxcOperation $message)
    {
	// Get passed optional parameters
	$name = null;
	if (strlen($message->getName())) {
            $name = $message->getName();
        }
	// Get passed optional parameters
	$os = null;
	if (strlen($message->getOS())) {
            $os = $message->getOS();
        }
	// Get passed optional parameters
	$hp = null;
	if (strlen($message->getHP())) {
            $hp = $message->getHP();
        }
        $environment = null;
	if (strlen($message->getEnvironmentId())) {
            $environment = $this->environmentRepository->find($message->getEnvironmentId());
        }

        $instance = null;
	if (strlen($message->getInstanceId())) {
            $instance = $this->instanceRepository->find($message->getInstanceId());
        }

        $instance_type = null;
	if (strlen($message->getInstanceTypeId())) {
            $instance_type = $this->instanceTypeRepository->find($message->getInstanceTypeId());
        }

        // Switch command to serve
	switch( $message->getCommand()) {

	// Binding some orphan instance to an env
	case "bind":

	  // REQUIRED: EnvID and InstanceTypeId
	  if(!$instance_type || !$environment) {
            $this->logger->error( "Instance type ID and Env ID are required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  // Try to find an available Instance
	  $instance_status = $this->instanceStatusRepository->findOneByStatus("Started");
          $instance = $this->instanceRepository->findOneBy(["instance_type_id" => $instance_type->getId(),
		"instance_status_id" => $instance_status->getId()]);

	  // Started instance of necessary type was found
	  if($instance) {

	    // Bind an instance to an environment
	    $environment->setInstance($instance);

	    // Store item into the DB
	    $this->entityManager->persist($environment);
	    $this->entityManager->flush();

	  } else {

	      // Send message to request creation of the instance of the certain type
              $this->lxdOperationBus->dispatch(new LxcOperation(["command" => "create",
                "environment_id" => $environment->getId(),
                "instance_type_id" => $instance_type->getId()]));

	      // Send message to request deployment of the env
              $this->awxBus->dispatch(new RunPlaybook(["name" => "deploy",
                "environment_id" => $environment->getId()]));
	  }


	  break;

	// Creating new LXC instance
	case "create":

	  // REQUIRED: InstanceTypeId
	  if(!$os || !$hp) {
            $this->logger->error( "OS & HP is required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  $this->logger->debug( "Creating LXC instance, OS alias: `" . $os . "`, HW profile: `" . $hp . "`");
	  $responce = $this->lxd->createInstance($os, $hp);

	  break;
          
	case "restart":

	  // REQUIRED: name
	  if(!$name) {
            $this->logger->error( "Name is required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  # TODO: Check state: can not stop already stopped unless forced

	  $this->logger->debug( "Handling `" . $message->getCommand() . "` command for LXC object: `" . $name . "`");
	  $responce = $this->lxd->restartInstance($name);    
          
	  break;
          
	case "start":

	  // REQUIRED: name
	  if(!$name) {
            $this->logger->error( "Name is required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  # TODO: Check state: can not stop already stopped unless forced

	  $this->logger->debug( "Handling `" . $message->getCommand() . "` command for LXC object: `" . $name . "`");
	  $responce = $this->lxd->startInstance($name);
            
	  break;
          
	case "stop":

	  // REQUIRED: name
	  if(!$name) {
            $this->logger->error( "Name is required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  # TODO: Check state: can not stop already stopped unless forced

	  $this->logger->debug( "Handling `" . $message->getCommand() . "` command for LXC object: `" . $name . "`");
	  $responce = $this->lxd->stopInstance($name);
  
	  break;

	case "delete":
            
	  // REQUIRED: name
	  if(!$name) {
            $this->logger->error( "Name is required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  $this->logger->debug( "Handling `" . $message->getCommand() . "` command for LXC object: `" . $name . "`");
	  $responce = $this->lxd->deleteInstance($name);	

	  break;

	case "wipe":

	  $this->logger->debug( "Handling `" . $message->getCommand() . "` command");
	  $responce = $this->lxd->deleteAllInstances(true);	

	  break;

	default:
            $this->logger->debug( "Unknown command: `" . $message->getCommand() . "`");
	  break;
	}

        // do something with your message
    }
}
