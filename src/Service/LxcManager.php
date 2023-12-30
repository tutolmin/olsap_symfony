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
    private $instanceRepository;

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
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);

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

    private function initInstance($os_alias, $hp_name) {

        $this->logger->debug(__METHOD__);

        // look for a specific OperatingSystems object
        $os = $this->osRepository->findOneByAlias($os_alias);

        // look for a specific HardwareProfiles object
        $hp = $this->hpRepository->findOneByName($hp_name);

        // Both OS and HW profile objects found
        if (!$os || !$hp) {
            $this->logger->debug("OS alias or HW profile name is invalid. Check your input!");
            return null;
        }

        // look for the instance type
        $instance_type = $this->itRepository->findOneBy(array('os' => $os->getId(), 'hw_profile' => $hp->getId()));

        // Both OS and HW profile objects found
        if (!$instance_type) {
            $this->logger->debug("Instance type id was not found in the database for valid OS and HW profile. Run `app:instance-types:populate` command");
            return null;
        }

        $instance = new Instances;

        // It is New/Started by sefault
        $instance_status = $this->instanceStatusRepository->findOneByStatus("New");
        $instance->setStatus($instance_status);
        $instance->setInstanceType($instance_type);
        $instance->setName(bin2hex(random_bytes(10)));
        $this->logger->debug("Generated Instance name: " . $instance->getName());
        $this->entityManager->persist($instance);

        // Find an address item which is NOT linked to any instance
        $address = $this->addressRepository->findOneByInstance(null);
        $address->setInstance($instance);

        // TODO: catch no address available exception
        // TODO: same address can be allocated to multiple new instances on a race condidion

        $this->logger->debug("Selected address: " . $address->getIp() . ", MAC: " . $address->getMac());
        $this->entityManager->flush();

        return $instance->getId();
    }

    public function createObject($os_alias, $hp_name) {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        $this->logger->debug("Creating LXC object: OS: `" . $os_alias . "`, HW profile: `" . $hp_name . "`");

        $instance_id = $this->initInstance($os_alias, $hp_name);
        $instance = $this->instanceRepository->findOneById($instance_id);

        if (!$instance) {
            $this->logger->debug("Instance creation failure");
            return false;
        }

//        $addresses = $instance->getAddresses();
        $address = $this->addressRepository->findOneByInstance($instance);

 //       $this->logger->debug($address->getMac());
//        $this->logger->debug("Address: ".$addresses->current()->getMac());

        // Create an object in LXC
        $options = [
            'alias' => $os_alias,
            'profiles' => [$hp_name],
            "config" => [
                "volatile.eth0.hwaddr" => $address->getMac()
            ],
        ];
        $responce = $this->lxd->containers->create(null, $options, $this->wait);

        //Catch exception
        // Get the name for the reply
        $name = explode("/", $responce["resources"]["containers"][0]);

        $this->logger->debug("Created object: " . $name[3]);

        $instance->setName($name[3]);

        // Store item into the DB
//        $this->entityManager->persist($instance);
        $this->entityManager->flush();

        $this->lxdOperationBus->dispatch(new LxcOperation(["command" => "start", "name" => $name[3]]));

        return $name[3];
    }

    public function startObject($name, $force = false) {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        $this->logger->debug("Starting LXC object: `" . $name . "`, timeout: " . $this->timeout . ", force: " . ($force ? "true" : "false"));

        $info = $this->getObjectInfo($name);

        if ($info && $info["status"] != "Started") {
            $responce = $this->lxd->containers->start($name, $this->timeout, $force, false, $this->wait);
            $this->logger->debug('Dispatching LXC event message');
            $this->lxdEventBus->dispatch(new LxcEvent(["event" => "started", "name" => $name]));
            return $responce;
        }

        //TODO: Handle exception

        return null;
    }

    public function stopObject($name, $force = false) {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        $this->logger->debug("Stopping LXC object: `" . $name . "`, timeout: " . $this->timeout . ", force: " . ($force ? "true" : "false"));

        $info = $this->getObjectInfo($name);

        if ($info && $info["status"] != "Stopped") {
            $responce = $this->lxd->containers->stop($name, $this->timeout, $force, false, $this->wait);
            $this->logger->debug('Dispatching LXC event message');
            $this->lxdEventBus->dispatch(new LxcEvent(["event" => "stopped", "name" => $name]));
            return $responce;
        }

        //TODO: Handle exception

        return null;
    }

    public function restartObject($name, $force = false) {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        $this->logger->debug("Restarting LXC object: `" . $name . "`, timeout: " . $this->timeout . ", force: " . ($force ? "true" : "false"));

        $info = $this->getObjectInfo($name);

        if ($info) {
            $responce = $this->lxd->containers->restart($name, $this->timeout, $force, false, $this->wait);
            $this->logger->debug('Dispatching LXC event message');
            $this->lxdEventBus->dispatch(new LxcEvent(["event" => "started", "name" => $name]));
            return $responce;
        }

        //TODO: Handle exception

        return null;
    }

    public function deleteObject($name, $force = false) {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        if($this->wipeInstance($name, $force))
        {
            $this->logger->debug("Instance ".$name." was deleted successfully");
        } else {
            $this->logger->debug("Instance ".$name." deletion failure");
        }
                
        if($this->wipeObject($name, $force))
        {
            $this->logger->debug("Object ".$name." was deleted successfully");
        } else {
            $this->logger->debug("Object ".$name." deletion failure");
        }
        
        return true;
    }

    public function deleteInstance($name, $force = false) {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        $this->deleteObject($name, $force);

        return true;
    }

    private function wipeObject($name, $force = false) {
        $this->logger->debug("Deleting LXC object: `" . $name . "`");

        $object = $this->getObjectInfo($name);

        if (!$object) {
            $this->logger->debug("Object NOT found");
            return false;
        }     

        if ($object['status'] != "Stopped") {
            $this->logger->debug("Object is NOT stopped");
/*
 * https://documentation.ubuntu.com/lxd/en/latest/api/#/
 * 
 * DELETE instance API does NOT have force option
 * 
            if (!$force) {
                return false;
            } else {
                $this->logger->debug("Force opiton specified");
            }
 * 
 */
        }
        
        try {
            $this->lxd->containers->remove($name, $this->wait);
        } catch (NotFoundException $exc) {
            $this->logger->debug("LXC object `" . $name . "` does not exist!");
            $this->logger->debug($exc->getTraceAsString());
        }
        return true;
    }

    private function wipeInstance($name, $force = false)
    {
        // look for a specific Instance 
        $instance = $this->instanceRepository->findOneByName($name);

        if (!$instance) {
            $this->logger->debug("Instance NOT found");
            return false;
        }

        if ($instance->getStatus()->getStatus() != "Stopped" &&
                $instance->getStatus()->getStatus() != "Sleeping") {
            $this->logger->debug("Instance is NOT stopped");
            if (!$force) {
                return false;
            } else {
                $this->logger->debug("Force opiton specified");
            }
        }

        // Unbind an instance from env so it can be used again
        $instance->setEnvs(null);

        // Fetch all linked Addresses and release them
        $addresses = $instance->getAddresses();
        foreach ($addresses as $address) {
            $address->setInstance(null);
//            $this->entityManager->flush();
        }

        // Delete item from the DB
        $this->entityManager->remove($instance);
        $this->entityManager->flush();
        
        return true;
    }
    
    public function deleteAllObjects($force = false) {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        $objects = $this->getObjectList();

        $result = true;

        if (!$objects) {
            $this->logger->debug("No objects to delete");
        }

        foreach ($objects as $object) {
            $info = $this->getObjectInfo($object);
            if (!$this->deleteObject($info['name'], $force)) {
                $result = false;
            }
        }

        return $result;
    }    
    
    public function deleteAllInstances($force = false) {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        $instances = $this->getInstanceList();

        $result = true;

        if (!$instances) {
            $this->logger->debug("No instances to delete");
        }

        foreach ($instances as $instance) {
            if (!$this->deleteInstance($instance->getName(), $force)) {
                $result = false;
            }
        }

        return $result;
    }

    public function getObjectInfo($name)//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

	// TODO: check container existence - input validation
        
        try {
            $object = $this->lxd->containers->info($name);  
            $this->logger->debug( "Object `" . $name . "` status: ".$object['status']);
            return $object;
        } catch (NotFoundException $exc) {
            $this->logger->debug( "Object `" . $name . "` does not exist!");
            $this->logger->debug( $exc->getTraceAsString());
        }
        return null;
    }
    
    public function getObjectList() {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        $objects = $this->lxd->containers->all();

        // TODO: handle exception

        if (count($objects)) {
            return $objects;
        } else {
            return NULL;
        }
    }
    
    public function getInstanceList() {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        $instances = $this->instanceRepository->findAll();

        // TODO: handle exception

        if (count($instances)) {
            return $instances;
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

