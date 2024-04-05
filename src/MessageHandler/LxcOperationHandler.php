<?php

namespace App\MessageHandler;

use App\Message\LxcOperation;
#use App\Message\LxcEvent;
//use App\Message\RunPlaybook;
#use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
//use Symfony\Component\Messenger\MessageBusInterface;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InstancesRepository;
use App\Entity\InstanceStatuses;
use App\Entity\Instances;
use App\Entity\InstanceTypes;
//use App\Entity\Environments;

use Psr\Log\LoggerInterface;

use App\Service\LxcManager;

/*
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;
*/
#[AsMessageHandler(fromTransport: 'async', bus: 'lxc.operation.bus')]
final class LxcOperationHandler
{
    // Logger reference
    private LoggerInterface $logger;

    // Doctrine EntityManager
    private EntityManagerInterface $entityManager;

//    private $message;
//    private $environmentRepository;
    private $instanceTypeRepository;
    private $instanceStatusRepository;
    private InstancesRepository $instanceRepository;

    // Message bus
//    private $awxBus;
//    private $lxdEventBus;
//    private $lxcOperationBus;

    private LxcManager $lxcService;

    public function __construct(
	LoggerInterface $logger, EntityManagerInterface $entityManager,
//	MessageBusInterface $awxBus, 
//        MessageBusInterface $lxdEventBus, 
//        MessageBusInterface $lxcOperationBus, 
        LxcManager $lxcService)
    {
        $this->logger = $logger;
//        $this->awxBus = $awxBus;
//        $this->lxdEventBus = $lxdEventBus;
//        $this->lxcOperationBus = $lxcOperationBus;
	$this->lxcService = $lxcService;

        $this->entityManager = $entityManager;
        $this->instanceTypeRepository = $this->entityManager->getRepository( InstanceTypes::class);
//        $this->environmentRepository = $this->entityManager->getRepository( Environments::class);
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
	$operating_system = null;
	if (strlen($message->getOS())) {
            $operating_system = $message->getOS();
        }
	// Get passed optional parameters
	$hardware_profile = null;
	if (strlen($message->getHP())) {
            $hardware_profile = $message->getHP();
        }
        $environment_id = null;
	if ($message->getEnvironmentId()>0) {
            $environment_id = $message->getEnvironmentId();
        }

        $instance_status = null;
	if (strlen($message->getInstanceStatus())) {
            $instance_status = $this->instanceStatusRepository->findOneByStatus($message->getInstanceStatus());
        }
        
        $instance = null;
	if ($message->getInstanceId()>0) {
            $instance = $this->instanceRepository->find($message->getInstanceId());
        }

        $instance_type = null;
	if ($message->getInstanceTypeId()>0) {
            $instance_type = $this->instanceTypeRepository->find($message->getInstanceTypeId());
        }

        // Switch command to serve
	switch( $message->getCommand()) {
/*
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
              $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "create",
                "environment_id" => $environment->getId(),
                "instance_type_id" => $instance_type->getId()]));

	      // Send message to request deployment of the env
              $this->awxBus->dispatch(new RunPlaybook(["name" => "deploy",
                "environment_id" => $environment->getId()]));
	  }

	  break;
*/
	// Creating new LXC object
	case "create":

	  if(!$operating_system || !$hardware_profile) {
            $this->logger->error( "OS & HP is required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  $this->logger->debug( "Creating LXC object, OS alias: `" . $operating_system . 
                  "`, HW profile: `" . $hardware_profile . "`");
	  $this->lxcService->create($operating_system, $hardware_profile, $environment_id, false);

	  break;
          
	case "restart":

	  // REQUIRED: name
	  if(!$name) {
            $this->logger->error( "Name is required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  # TODO: Check state: can not stop already stopped unless forced

	  $this->logger->debug( "Handling `" . $message->getCommand() . "` command for LXC object: `" . $name . "`");
	  $responce = $this->lxcService->restart($name, false, false);    
          
	  break;
          
	case "start":

	  // REQUIRED: name
	  if(!$name) {
            $this->logger->error( "Name is required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  # TODO: Check state: can not stop already stopped unless forced

	  $this->logger->debug( "Handling `" . $message->getCommand() . "` command for LXC object: `" . $name . "`");
	  $responce = $this->lxcService->start($name, false, false);
            
	  break;
          
	case "stop":

	  // REQUIRED: name
	  if(!$name) {
            $this->logger->error( "Name is required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  # TODO: Check state: can not stop already stopped unless forced

	  $this->logger->debug( "Handling `" . $message->getCommand() . "` command for LXC object: `" . $name . "`");
	  $responce = $this->lxcService->stop($name, false, false);
  
	  break;

	case "delete":
            
	  // REQUIRED: name
	  if(!$name) {
            $this->logger->error( "Name is required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  $this->logger->debug( "Handling `" . $message->getCommand() . "` command for LXC object: `" . $name . "`");
	  $responce = $this->lxcService->deleteObject($name);	

	  break;
        
	case "deleteAll":

	  $this->logger->debug( "Handling `" . $message->getCommand() . "` command");
	  $responce = $this->lxcService->deleteAllObjects(true);	

	  break;

        case "setInstanceStatus":

	  // REQUIRED: name
	  if(!$name) {
            $this->logger->error( "Name is required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  $this->logger->debug( "Handling `" . $message->getCommand() . "` command for LXC object: `" . $name . "`");
	  $responce = $this->lxcService->setInstanceStatus($name, $instance_status);	            
          
          break;

	default:
            $this->logger->debug( "Unknown command: `" . $message->getCommand() . "`");
	  break;
	}

        // do something with your message
    }
}
