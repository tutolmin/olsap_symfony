<?php

namespace App\MessageHandler;

use App\Message\LxcOperation;
use App\Message\RunPlaybook;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
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
#[AsMessageHandler]
final class LxcOperationHandler implements MessageHandlerInterface
{
    // Logger reference
    private $logger;

    // Doctrine EntityManager
    private $entityManager;

    private $message;
    private $environmentRepository;
    private $instanceTypeRepository;
    private $instanceStatusRepository;
    private $instanceRepository;

    // Message bus
    private $bus;

    private $lxd;

    public function __construct(
	LoggerInterface $logger, EntityManagerInterface $entityManager,
	MessageBusInterface $bus, LxcManager $lxd)
    {
        $this->logger = $logger;
        $this->bus = $bus;
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
	$environment = null;
	if( strlen($message->getEnvironmentId()))
          $environment = $this->environmentRepository->find($message->getEnvironmentId());

	$instance = null;
	if( strlen($message->getInstanceId()))
          $instance = $this->instanceRepository->find($message->getInstanceId());

        $instance_type = null;
	if( strlen($message->getInstanceTypeId()))
	  $instance_type = $this->instanceTypeRepository->find($message->getInstanceTypeId());

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
              $this->bus->dispatch(new LxcOperation(["command" => "create",
                "environment_id" => $environment->getId(), "instance_id" => null,
                "instance_type_id" => $instance_type->getId()]));

	      // Send message to request deployment of the env
              $this->bus->dispatch(new RunPlaybook(["name" => "deploy",
                "environment_id" => $environment->getId()]));

	  }


	  break;

	// Creating new LXC instance
	case "create":

	  // REQUIRED: InstanceTypeId
	  if(!$instance_type) {
            $this->logger->error( "Instance type ID is required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  $this->logger->debug( "Creating LXC instance of type id: `" . $instance_type->getId() . "`, OS alias: `" . 
	      $instance_type->getOs()->getAlias() . "`, HW profile: `" . $instance_type->getHwProfile()->getName() . "`");

/*
	  // Create an instance in LXD
	  $options = [
	      'alias'  => $instance_type->getOs()->getAlias(),
	      'profiles' => [$instance_type->getHwProfile()->getName() ]
	  ];
	  $responce = $this->lxd->containers->create(null, $options);	
*/
	  $responce = $this->lxd->createInstance($instance_type->getOs()->getAlias(),
		$instance_type->getHwProfile()->getName());

	  # TODO: handle exception
/*
	  // Get the name for the reply
	  $name=explode( "/", $responce["resources"]["containers"][0]);

	  $instance = new Instances;
	  $instance->setName($name[3]);
	  $instance_status = $this->instanceStatusRepository->findOneByStatus("Stopped");
	  $instance->setStatus($instance_status);
	  $instance->setInstanceType($instance_type);
	  $now = new \DateTimeImmutable('NOW');
	  $instance->setCreatedAt($now);

	  // Store item into the DB
	  $this->entityManager->persist($instance);
	  $this->entityManager->flush();

	  // Created instance for particular Environment
	  if($environment) {

	    // Bind an instance to an environment
	    $environment->setInstance($instance);

	    // Store item into the DB
	    $this->entityManager->persist($environment);
	    $this->entityManager->flush();
	  }

	  # TODO: Handle exception

	  // Send message to start a container
	  $this->bus->dispatch(new LxcOperation(["command" => "start",
	    "environment_id" => null, "instance_id" => $instance->getId(),
	    "instance_type_id" => null]));
*/
	  break;

	case "start":

	  // REQUIRED: InstanceId
	  if(!$instance) {
            $this->logger->error( "Instance ID is required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  # TODO: Check state: can not start already started unless forced

	  $this->logger->debug( "Starting LXC instance: `" . $instance->getName() . "`");

//	  $responce = $this->lxd->containers->start($instance->getName());	
	  $responce = $this->lxd->startInstance($instance->getName());	

	  # TODO: handle exception

          $instance = $this->instanceRepository->findOneById($instance);
	  $instance_status = $this->instanceStatusRepository->findOneByStatus("Started");
	  $instance->setStatus($instance_status);

	  // Store item into the DB
	  $this->entityManager->persist($instance);
	  $this->entityManager->flush();

	  # TODO: Handle exception

	  break;

	case "stop":

	  // REQUIRED: InstanceId
	  if(!$instance) {
            $this->logger->error( "Instance ID is required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  # TODO: Check state: can not stop already stopped unless forced

	  $this->logger->debug( "Stopping LXC instance: `" . $instance->getName() . "`");

//	  $responce = $this->lxd->containers->stop($instance->getName());	
	  $responce = $this->lxd->stopInstance($instance->getName());	

	  # TODO: handle exception

          $instance = $this->instanceRepository->findOneById($instance);
	  $instance_status = $this->instanceStatusRepository->findOneByStatus("Stopped");
	  $instance->setStatus($instance_status);

	  // Store item into the DB
	  $this->entityManager->persist($instance);
	  $this->entityManager->flush();

	  # TODO: Handle exception

	  break;

	case "delete":

	  // REQUIRED: InstanceId
	  if(!$instance) {
            $this->logger->error( "Instance ID is required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  # TODO: Check state: can not delete running container unless forced

	  $this->logger->debug( "Deleting LXC instance: `" . $instance->getName() . "`");

//	  $responce = $this->lxd->containers->remove($instance->getName());	
	  $responce = $this->lxd->deleteInstance($instance->getName());	

	  # TODO: handle exception

          $instance = $this->instanceRepository->findOneById($instance);

	  // Store item into the DB
	  $this->entityManager->remove($instance);
	  $this->entityManager->flush();

	  # TODO: Handle exception

	  break;

	default:
            $this->logger->debug( "Unknown command: `" . $message->getCommand() . "`");
	  break;
	}

        // do something with your message
    }
}
