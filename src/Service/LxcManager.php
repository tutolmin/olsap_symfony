<?php

// src/Service/LxcManager.php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;


#use App\Entity\Tasks;
#use App\Entity\InstanceTypes;

class LxcManager
{
    private $logger;

    private $lxd;

    private $entityManager;
//    private $taskRepository;

    public function __construct( LoggerInterface $logger, EntityManagerInterface $em)
    {
        $this->logger = $logger;
        $this->entityManager = $em;

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

        #$certificates = $lxd->certificates->all();
        #$fingerprint = $lxd->certificates->add(file_get_contents(__DIR__.'/client.pem'), 'ins3Cure');

        #$info = $lxd->host->info();
        #var_dump( $info);

        /*
        if ($lxd->host->trusted()) {
            echo 'trusted';
        } else {
            echo 'not trusted';
        }
	*/
        // get the task repository
//        $this->taskRepository = $this->entityManager->getRepository( Tasks::class);
    }


    public function createInstance($os_alias, $hw_name)//: ?InstanceTypes
    {  

//        $this->logger->debug( "Starting LXC instance: `" . $instance->getName() . "`");
        $this->logger->debug( "Creating LXC instance: OS: `" . $os_alias . "`, HW profile: `" . $hw_name . "`");

	// Create an instance in LXD
	$options = [
	    'alias'  => $os_alias,
	    'profiles' => [$hw_name]
	];
	$responce = $this->lxd->containers->create(null, $options);

	//TODO: Handle exception

	return $responce;

    }

    public function startInstance($instance)//: ?InstanceTypes
    {  

        $this->logger->debug( "Starting LXC instance: `" . $instance . "`");

	$responce = $this->lxd->containers->start($instance);

	//TODO: Handle exception

	return $responce;

    }

    public function stopInstance($instance)//: ?InstanceTypes
    {  

        $this->logger->debug( "Stopping LXC instance: `" . $instance . "`");

	$responce = $this->lxd->containers->stop($instance);

	//TODO: Handle exception

	return $responce;

    }

    public function restartInstance($instance)//: ?InstanceTypes
    {  
	$this->stopInstance($instance);

	$this->startInstance($instance);

	return NULL;
    }

    public function deleteInstance($instance)//: ?InstanceTypes
    {  

        $this->logger->debug( "Deleting LXC instance: `" . $instance . "`");

	$responce = $this->lxd->containers->remove($instance);

	//TODO: Handle exception

	return $responce;

    }

    public function getInstanceInfo($container)//: ?InstanceTypes
    {  
	// TODO: check container existence - input validation

	return $this->lxd->containers->info($container);
    }

    public function getInstanceList()//: ?InstanceTypes
    {  
        $containers = $this->lxd->containers->all();

	// TODO: handle exception

	if(count($containers))

          return $containers;

	else

	  return NULL;
    }

    public function getImageInfo($image)//: ?InstanceTypes
    {  
	// TODO: check image existence - input validation

	return $this->lxd->images->info($image);
    }

    public function getImageList()//: ?InstanceTypes
    {  
        $images = $this->lxd->images->all();

	// TODO: handle exception

	if(count($images))

          return $images;

	else

	  return NULL;
    }

    public function getProfileInfo($profile)//: ?InstanceTypes
    {  
	// TODO: check image existence - input validation

	return $this->lxd->profiles->info($profile);
    }

    public function getProfileList()//: ?InstanceTypes
    {  
        $profiles = $this->lxd->profiles->all();

	// TODO: handle exception

	if(count($profiles))

          return $profiles;

	else

	  return NULL;
    }

}

