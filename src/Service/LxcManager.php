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

class LxcManager
{
    private $logger;
    private $lxcEventBus;
    private $lxcOperationBus;
    private $lxcService;
    private $timeout;
    private $wait;

    // Doctrine EntityManager
    private $entityManager;

    // InstanceTypes repo
    private $itRepository;
    private $instanceStatusesRepository;
    private $instanceRepository;

    // OperatingSystems repo
    private $osRepository;

    // HardwareProfiles repo
    private $hpRepository;

    // Addresses repo    
    private $addressRepository;

    public function __construct( LoggerInterface $logger, EntityManagerInterface $entityManager,
            MessageBusInterface $lxcEventBus, MessageBusInterface $lxcOperationBus)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $entityManager;

        // get the InstanceTypes repository
        $this->itRepository = $this->entityManager->getRepository( InstanceTypes::class);
        $this->instanceStatusesRepository = $this->entityManager->getRepository(InstanceStatuses::class);
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);

        // get the OperatingSystems repository
        $this->osRepository = $this->entityManager->getRepository( OperatingSystems::class);

        // get the HardwareProfiles repository
        $this->hpRepository = $this->entityManager->getRepository( HardwareProfiles::class);

        // get the Addresses repository
        $this->addressRepository = $this->entityManager->getRepository( Addresses::class);
        
        $this->lxcEventBus = $lxcEventBus;
        $this->lxcOperationBus = $lxcOperationBus;
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
        $this->lxcService = new \Opensaucesystems\Lxd\Client($adapter);
        $this->lxcService->setUrl($_ENV['LXD_URL']);

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
        $instance_status = $this->instanceStatusesRepository->findOneByStatus("New");
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

        // This will trigger DB flush
        // So we can get the Instance from the repo by id.
        return $instance->getId();
//        return $instance;
    }

    public function create($os_alias, $hp_name, bool $async = true): ?Instances {
        $this->logger->debug(__METHOD__);

        if ($async) {
            $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "create",
                        "os" => $os_alias, "hp" => $hp_name]));
            return null;
        }

        $this->logger->debug("Creating LXC object: OS: `" . $os_alias . "`, HW profile: `" . $hp_name . "`");

        $instance_id = $this->initInstance($os_alias, $hp_name);
        $instance = $this->instanceRepository->findOneById($instance_id);

        if (!$instance) {
            $this->logger->debug("Instance creation failure");
            return null;
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
        $responce = $this->lxcService->containers->create(null, $options, $this->wait);

        //Catch exception
        // Get the name for the reply
        $name = explode("/", $responce["resources"]["containers"][0]);

        $this->logger->debug("Created object: " . $name[3]);

        $instance->setName($name[3]);

        // Store item into the DB
//        $this->entityManager->persist($instance);
        $this->entityManager->flush();

        $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "start", "name" => $name[3]]));

        return $instance;
    }

    public function start($name, $force = false, bool $async = true): bool {
        $this->logger->debug(__METHOD__);

        if ($async) {
            $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "start", "name" => $name]));
            return true;
        }

        $this->logger->debug("Starting LXC object: `" . $name . "`, timeout: " . 
                $this->timeout . ", force: " . ($force ? "true" : "false"));

        $info = $this->getObjectInfo($name);

        if ($info && $info["status"] != "Running") {
            $this->lxcService->containers->start($name, $this->timeout, $force, false, $this->wait);
            $this->logger->debug('Dispatching LXC event message');
            $this->lxcEventBus->dispatch(new LxcEvent(["event" => "started", "name" => $name]));
            return true;
        }

        //TODO: Handle exception

        return false;
    }

    public function stop($name, $force = false, bool $async = true): bool {
        $this->logger->debug(__METHOD__);
        
        if ($async) {
            $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "stop", "name" => $name]));
            return true;
        }
        
        $this->logger->debug("Stopping LXC object: `" . $name . "`, timeout: " . 
                $this->timeout . ", force: " . ($force ? "true" : "false"));

        $info = $this->getObjectInfo($name);

        if ($info && $info["status"] != "Stopped") {
            $this->lxcService->containers->stop($name, $this->timeout, $force, false, $this->wait);
            $this->logger->debug('Dispatching LXC event message');
            $this->lxcEventBus->dispatch(new LxcEvent(["event" => "stopped", "name" => $name]));
            return true;
        }

        //TODO: Handle exception

        return false;
    }

    public function restart($name, $force = false, bool $async = true): bool {
        $this->logger->debug(__METHOD__);
        
        if ($async) {
            $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "restart", "name" => $name]));
            return true;
        }
        
        $this->logger->debug("Restarting LXC object: `" . $name . "`, timeout: " . 
                $this->timeout . ", force: " . ($force ? "true" : "false"));

        $info = $this->getObjectInfo($name);

        if ($info) {
            $this->lxcService->containers->restart($name, $this->timeout, $force, false, $this->wait);
            $this->logger->debug('Dispatching LXC event message');
            $this->lxcEventBus->dispatch(new LxcEvent(["event" => "started", "name" => $name]));
            return true;
        }

        //TODO: Handle exception

        return false;
    }

    public function deleteObject($name, $force = false) {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        $result = true;
        if ($this->wipeInstance($name, $force)) {
            $this->logger->debug("Instance " . $name . " was deleted successfully");
        } else {
            $this->logger->debug("Instance " . $name . " deletion failure");
            $result = false;
        }

        if ($this->wipeObject($name, $force)) {
            $this->logger->debug("Object " . $name . " was deleted successfully");
        } else {
            $this->logger->debug("Object " . $name . " deletion failure");
            $result = false;
        }
        return $result;
    }

    public function deleteInstance($name, $force = false, bool $async = true) {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        if ($async) {
            $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "delete",
                        "name" => $name]));
        } else {
            return $this->deleteObject($name, $force);
        }
    }

    private function wipeObject($name, $force = false) {
        $this->logger->debug(__METHOD__);

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
             */
            if (!$force) {
                return false;
            } else {
                $this->logger->debug("Force opiton specified");
            }
        }

        $this->stop($name, $force, false);

        try {
            $this->lxcService->containers->remove($name, $this->wait);
        } catch (NotFoundException $exc) {
            $this->logger->debug("LXC object `" . $name . "` does not exist!");
            $this->logger->debug($exc->getTraceAsString());
        }
        return true;
    }

    private function wipeInstance($name, $force = false)
    {
        $this->logger->debug(__METHOD__);

         // look for a specific Instance 
        $instance = $this->instanceRepository->findOneByName($name);

        if (!$instance) {
            $this->logger->debug("Instance NOT found");
            return false;
        }

        if ($instance->getStatus() != "Stopped" &&
                $instance->getStatus() != "Sleeping") {
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
            return false;
        }

        foreach ($objects as $object) {
            $info = $this->getObjectInfo($object);
            if (!$this->deleteObject($info['name'], $force)) {
                $result = false;
            }
        }

        return $result;
    }    
    
    public function deleteAllInstances($force = false, bool $async = true) {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        $instances = $this->getInstanceList();

        $result = true;

        if (!$instances) {
            $this->logger->debug("No instances to delete");
            return false;
        }

        foreach ($instances as $instance) {
            if ($async) {
                $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "delete", 
                    "name" => $instance->getName()]));
            } else {
                if (!$this->deleteInstance($instance->getName(), $force)) {
                    $result = false;
                }
            }
        }

        return $result;
    }

    public function getObjectInfo($name)//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

	// TODO: check container existence - input validation
        
        try {
            $object = $this->lxcService->containers->info($name);  
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

        $objects = $this->lxcService->containers->all();

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

	return $this->lxcService->images->info($image);
    }

    public function getImageList()//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

        $images = $this->lxcService->images->all();

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

	return $this->lxcService->profiles->info($profile);
    }

    public function getProfileList()//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

        $profiles = $this->lxcService->profiles->all();

	// TODO: handle exception

	if (count($profiles)) {
            return $profiles;
        } else {
            return NULL;
        }
    }

    public function setInstanceStatus(int $instance_id, $status_str): bool {

        $this->logger->debug(__METHOD__);

        $status = $this->instanceStatusesRepository->findOneByStatus($status_str);

        if (!$status) {
            $this->logger->debug('No such instance status: ' . $status_str);
            return false;
        }

        $instance = $this->instanceRepository->findOneById($instance_id);

        if (!$instance) {
            $this->logger->debug('No such instance!');
            return false;
        }

        // Special statuses for bound instances
        $target_status = $this->tweakInstanceStatus($instance_id, $status_str);

        $this->logger->debug('Changing instance ' . $instance . ' status to: ' . $target_status);

        $instance->setStatus($target_status);

        // Store item into the DB
//	  $this->entityManager->persist($instance);
        $this->entityManager->flush();

        return true;
    }

    private function tweakInstanceStatus(int $instance_id, string $status_str): InstanceStatuses {

        $this->logger->debug(__METHOD__);

        $instance = $this->instanceRepository->findOneById($instance_id);

        $status = $this->instanceStatusesRepository->findOneByStatus($status_str);

        // Special statuses for bound instances
        $envs = $instance->getEnvs();
        if ($envs) {
            if ($status_str == "Started") {
                $status = $this->instanceStatusesRepository->findOneByStatus("Running");
            } elseif ($status_str == "Stopped") {
                $status = $this->instanceStatusesRepository->findOneByStatus("Sleeping");
            }
        } else {
            if ($status_str == "Running") {
                $status = $this->instanceStatusesRepository->findOneByStatus("Started");
            } elseif ($status_str == "Sleeping") {
                $status = $this->instanceStatusesRepository->findOneByStatus("Stopped");
            }
        }
        return $status;
    }
}

