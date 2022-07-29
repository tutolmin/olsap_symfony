<?php

namespace App\MessageHandler;

use App\Message\LxcOperation;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\InstanceStatuses;
use App\Entity\Instances;
use App\Entity\InstanceTypes;
use App\Entity\Environments;

use Psr\Log\LoggerInterface;

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;

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

    private $lxd;

    public function __construct(
//	EnvironmentsRepository $environmentRepository,
//	InstanceTypesRepository $instanceTypeRepository, 
	LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;

        $this->entityManager = $entityManager;
        $this->instanceTypeRepository = $this->entityManager->getRepository( InstanceTypes::class);
        $this->environmentRepository = $this->entityManager->getRepository( Environments::class);
        $this->instanceStatusRepository = $this->entityManager->getRepository( InstanceStatuses::class);
        $this->instanceRepository = $this->entityManager->getRepository( Instances::class);

//        $this->environmentRepository = $environmentRepository;
//        $this->instanceTypeRepository = $instanceTypeRepository;

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

	  // REQUIRED: EnvID and InstanceId
	  if(!$instance || !$environment) {
            $this->logger->error( "Instance ID and Env ID are required for `" . $message->getCommand() . "` LXD command");
	    break;
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

	  // Create an instance in LXD
	  $options = [
	      'alias'  => $instance_type->getOs()->getAlias(),
	      'profiles' => [$instance_type->getHwProfile()->getName() ]
	  ];
	  $responce = $this->lxd->containers->create(null, $options);	

	  # TODO: handle exception

	  // Get the name for the reply
	  $name=explode( "/", $responce["resources"]["containers"][0]);

	  $instance = new Instances;
	  $instance->setName($name[3]);
	  $instance_status = $this->instanceStatusRepository->findOneByStatus("Stopped");
	  $instance->setStatus($instance_status);
	  $instance->setInstanceType($instance_type);
	  $now = new \DateTimeImmutable('NOW');
	  $instance->setCreatedAt($now);

	  # TODO: port allocation routine
	  $instance->setPort(1123);

	  // Store item into the DB
	  $this->entityManager->persist($instance);
	  $this->entityManager->flush();

	  # TODO: Handle exception

	  break;

	case "start":

	  // REQUIRED: InstanceId
	  if(!$instance) {
            $this->logger->error( "Instance ID is required for `" . $message->getCommand() . "` LXD command");
	    break;
	  }

	  # TODO: Check state: can not start already started unless forced

	  $this->logger->debug( "Starting LXC instance: `" . $instance->getName() . "`");

	  $responce = $this->lxd->containers->start($instance->getName());	

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

	  $responce = $this->lxd->containers->stop($instance->getName());	

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

	  $responce = $this->lxd->containers->remove($instance->getName());	

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
