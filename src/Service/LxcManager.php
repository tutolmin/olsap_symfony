<?php

// src/Service/LxcManager.php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;
use App\Entity\Addresses;
use Opensaucesystems\Lxd\Exception\NotFoundException;

#use App\Entity\Tasks;
#use App\Entity\InstanceTypes;

class LxcManager
{
    private $logger;

    private $lxd;
    private $timeout;
    private $wait;

    private $name;

    private $entityManager;
    private $addressRepository;

    public function __construct( LoggerInterface $logger, EntityManagerInterface $em)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;

	$this->timeout = intval($_ENV["LXD_TIMEOUT"]);
	$this->wait = $_ENV["LXD_WAIT"];

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
        // get the address repository
        $this->addressRepository = $this->entityManager->getRepository( Addresses::class);
    }


    public function createInstance($os_alias, $hw_name, $mac)//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

	/* We can not select address for the instance here
	   since it SHOULD be connected to an Instance object.
	   So, we expect that a passed parameter is valid
	
	   The same applies to OS alias and HW profile.
	   Therse values should be verified earlier.
	   Possibly we could double check it here.
	*/
/*
        $this->logger->debug( "Creating LXC instance: OS: `" . $os_alias . "`, HW profile: `" . $hw_name . "`");

        // Find an address item which is NOT linked to any instance
        $address = $this->addressRepository->findOneByInstance(null);

        $this->logger->debug( "Selected address: " . $address->getIp() . ", MAC: " . $address->getMac());
*/
	// Create an instance in LXD
	$options = [
	    'alias'  => $os_alias,
	    'profiles' => [$hw_name],
            "config" => [
//               "volatile.eth0.hwaddr" => $address->getMac(),
               "volatile.eth0.hwaddr" => $mac,
	    ],
	];
	$responce = $this->lxd->containers->create(null, $options, $this->wait);

	// Get the name for the reply
	$name=explode( "/", $responce["resources"]["containers"][0]);

        $this->logger->debug("Created instance: ".$name[3]);

	//TODO: Handle exception
	// Why it was commented out?
	$this->startInstance($name[3]);

	return $name[3];

    }

    public function startInstance($name, $force=false)//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

        $this->logger->debug( "Starting LXC instance: `" . $name . "`");

	$info = $this->getInstanceInfo($name);

	$responce = null;
	if($info["status"] != "Started")
	    $responce = $this->lxd->containers->start($name, $this->timeout, $force, false, $this->wait);

	//TODO: Handle exception

	return $responce;

    }

    public function stopInstance($name, $force=false)//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

        $this->logger->debug( "Stopping LXC instance: `" . $name . "`, timeout: " . $this->timeout . ", force: " . ($force?"true":"false"));

	$info = $this->getInstanceInfo($name);

	$responce = null;
	if($info["status"] != "Stopped")
	    $responce = $this->lxd->containers->stop($name, $this->timeout, $force, false, $this->wait);

	//TODO: Handle exception

	return $responce;

    }

    public function restartInstance($name, $force=false)//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

	$this->stopInstance($name, $force);

	$this->startInstance($name, $force);

	return NULL;
    }

    public function deleteInstance($name, $force=false)//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

        $this->logger->debug( "Deleting LXC instance: `" . $name . "`");

	$info = $this->getInstanceInfo($name);

        if ($info) {
            
	if($info["status"] == "Stopped") {

	  $this->lxd->containers->remove($name, $this->wait);
	  return true;

	} else {

	  if($force) {

	    // Stop it first
	    $this->stopInstance($name, $force);
	    $this->lxd->containers->remove($name, $this->wait);
	    return true;

	  } else {

            $this->logger->debug( "Instance `" . $name . "` is " . $info["status"]);
	  }
	}
//        } else {
//            $this->logger->debug( "Instance `" . $name ."` does not exist");
        }

	//TODO: Handle exception

	return false;
    }

    public function deleteAllInstances($name, $force)//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

	$instances = $this->getInstanceList();

	$result = true;

	foreach($instances as $instance)
	  if( !$this->deleteInstance($this->getInstanceInfo($instance)["name"], $force))
	    $result = false;

	return $result;
    }

    public function getInstanceInfo($name)//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

	// TODO: check container existence - input validation
        $container = null;
        
        try {
            $container = $this->lxd->containers->info($name);       
        } catch (NotFoundException $exc) {
            $this->logger->debug( "Instance `" . $name . "` does not exist!");

//            echo $exc->getTraceAsString();
        }
        
        return $container;
    }
    
    public function getInstanceList()//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

        $containers = $this->lxd->containers->all();

	// TODO: handle exception

	if(count($containers))

          return $containers;

	else

	  return NULL;
    }

    public function getImageInfo($image)//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

	// TODO: check image existence - input validation

	return $this->lxd->images->info($image);
    }

    public function getImageList()//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

        $images = $this->lxd->images->all();

	// TODO: handle exception

	if(count($images))

          return $images;

	else

	  return NULL;
    }

    public function getProfileInfo($profile)//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

	// TODO: check image existence - input validation

	return $this->lxd->profiles->info($profile);
    }

    public function getProfileList()//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

        $profiles = $this->lxd->profiles->all();

	// TODO: handle exception

	if(count($profiles))

          return $profiles;

	else

	  return NULL;
    }

}

