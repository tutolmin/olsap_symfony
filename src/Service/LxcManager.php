<?php

// src/Service/LxcManager.php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;
use App\Entity\Addresses;
use App\Entity\Instances;
use App\Entity\InstanceStatuses;
use App\Entity\InstanceTypes;
use App\Entity\OperatingSystems;
use App\Entity\HardwareProfiles;
use Opensaucesystems\Lxd\Exception\NotFoundException;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\LxcEvent;
use App\Message\LxcOperation;

#use App\Entity\Tasks;
#use App\Entity\InstanceTypes;

class LxcManager
{
    private $logger;
    private $lxdEventBus;
    private $lxdOperationBus;
    private $lxd;
//    private $session;
    private $timeout;
    private $wait;

//    private $name;

    // Doctrine EntityManager
    private $entityManager;

    // InstanceTypes repo
    private $itRepository;
    private $instanceStatusRepository;

    // OperatingSystems repo
    private $osRepository;

    // HardwareProfiles repo
    private $hpRepository;

    // Addresses repo    
    private $addressRepository;

    public function __construct( LoggerInterface $logger, EntityManagerInterface $entityManager,
            MessageBusInterface $lxdEventBus, MessageBusInterface $lxdOperationBus)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $entityManager;

        // get the InstanceTypes repository
        $this->itRepository = $this->entityManager->getRepository( InstanceTypes::class);
        $this->instanceStatusRepository = $this->entityManager->getRepository(InstanceStatuses::class);

        // get the OperatingSystems repository
        $this->osRepository = $this->entityManager->getRepository( OperatingSystems::class);

        // get the HardwareProfiles repository
        $this->hpRepository = $this->entityManager->getRepository( HardwareProfiles::class);

        // get the Addresses repository
        $this->addressRepository = $this->entityManager->getRepository( Addresses::class);
        
        $this->lxdEventBus = $lxdEventBus;
        $this->lxdOperationBus = $lxdOperationBus;
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


    public function createInstance($os_alias, $hp_name)//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

        // look for a specific OperatingSystems object
        $os = $this->osRepository->findOneByAlias($os_alias);

        // look for a specific HardwareProfiles object
        $hp = $this->hpRepository->findOneByName($hp_name);

	// Both OS and HW profile objects found
	if (!$os || !$hp) {
            $this->logger->debug( "OS alias or HW profile name is invalid. Check your input!");
            return null;
        }   
        
        // look for the instance type
        $instance_type = $this->itRepository->findOneBy(array('os' => $os->getId(), 'hw_profile' => $hp->getId()));

	// Both OS and HW profile objects found
	if (!$instance_type) {
            $this->logger->debug( "Instance type id was not found in the database for valid OS and HW profile. Run `app:instance-types:populate` command");
            return null;
        }   

        $instance = new Instances;

	// It is New/Started by sefault
        $instance_status = $this->instanceStatusRepository->findOneByStatus("Stopped");
        $instance->setStatus($instance_status);
        $instance->setInstanceType($instance_type);
        
        $this->logger->debug( "Creating LXC instance: OS: `" . $os_alias . "`, HW profile: `" . $hp_name . "`");

        // Find an address item which is NOT linked to any instance
        $address = $this->addressRepository->findOneByInstance(null);
        $address->setInstance($instance);

        // TODO: catch no address available exception

        // TODO: same address can be allocated to multiple new instances on a race condidion
        
        $this->logger->debug( "Selected address: " . $address->getIp() . ", MAC: " . $address->getMac());

	// Create an instance in LXD
	$options = [
	    'alias'  => $os_alias,
	    'profiles' => [$hp_name],
            "config" => [
               "volatile.eth0.hwaddr" => $address->getMac(),
	    ],
	];
	$responce = $this->lxd->containers->create(null, $options, $this->wait);

        //Catch exception
        
	// Get the name for the reply
	$name=explode( "/", $responce["resources"]["containers"][0]);

        $this->logger->debug("Created instance: ".$name[3]);

        $instance->setName($name[3]);

        // Store item into the DB
        $this->entityManager->persist($instance);
        $this->entityManager->flush();

        $this->lxdOperationBus->dispatch(new LxcOperation(["command" => "start", "name" => $name[3]]));

	return $name[3];
    }

    public function startInstance($name, $force = false) {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        $this->logger->debug("Starting LXC instance: `" . $name . "`, timeout: " . $this->timeout . ", force: " . ($force ? "true" : "false"));

        $info = $this->getInstanceInfo($name);

        if ($info && $info["status"] != "Started") {
            $responce = $this->lxd->containers->start($name, $this->timeout, $force, false, $this->wait);
            $this->logger->debug('Dispatching LXC event message');
            $this->lxdEventBus->dispatch(new LxcEvent(["event" => "started", "name" => $name]));
            return $responce;
        }

        //TODO: Handle exception

        return null;
    }

    public function stopInstance($name, $force = false) {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        $this->logger->debug("Stopping LXC instance: `" . $name . "`, timeout: " . $this->timeout . ", force: " . ($force ? "true" : "false"));

        $info = $this->getInstanceInfo($name);

        if ($info && $info["status"] != "Stopped") {
            $responce = $this->lxd->containers->stop($name, $this->timeout, $force, false, $this->wait);
            $this->logger->debug('Dispatching LXC event message');
            $this->lxdEventBus->dispatch(new LxcEvent(["event" => "stopped", "name" => $name]));
            return $responce;
        }

        //TODO: Handle exception

        return null;
    }

    public function restartInstance($name, $force = false) {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        $this->logger->debug("Restarting LXC instance: `" . $name . "`, timeout: " . $this->timeout . ", force: " . ($force ? "true" : "false"));

        $info = $this->getInstanceInfo($name);

        if ($info) {
            $responce = $this->lxd->containers->restart($name, $this->timeout, $force, false, $this->wait);
            $this->logger->debug('Dispatching LXC event message');
            $this->lxdEventBus->dispatch(new LxcEvent(["event" => "started", "name" => $name]));
            return $responce;
        }

        //TODO: Handle exception

        return null;
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

    public function deleteAllInstances($force=false)//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

	$instances = $this->getInstanceList();

	$result = true;

	foreach ($instances as $instance) {
            if (!$this->deleteInstance($this->getInstanceInfo($instance)["name"], $force)) {
                $result = false;
            }
        }

        return $result;
    }

    public function getInstanceInfo($name)//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

	// TODO: check container existence - input validation
        
        try {
            $container = $this->lxd->containers->info($name);  
            $this->logger->debug( "Instance `" . $name . "` status: ".$container['status']);
            return $container;
        } catch (NotFoundException $exc) {
            $this->logger->debug( "Instance `" . $name . "` does not exist!");
            $this->logger->debug( $exc->getTraceAsString());
        }
        
        return null;
    }
    
    public function getInstanceList()//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

        $containers = $this->lxd->containers->all();

	// TODO: handle exception

	if (count($containers)) {
            return $containers;
        } else {
            return NULL;
        }
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

	if (count($images)) {
            return $images;
        } else {
            return NULL;
        }
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

	if (count($profiles)) {
            return $profiles;
        } else {
            return NULL;
        }
    }

}

