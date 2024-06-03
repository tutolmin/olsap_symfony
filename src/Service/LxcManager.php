<?php

// src/Service/LxcManager.php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InstancesRepository;
use App\Repository\InstanceStatusesRepository;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;
use App\Entity\Addresses;
use App\Entity\Breeds;
use App\Entity\Instances;
use App\Entity\InstanceStatuses;
use App\Entity\InstanceTypes;
use App\Entity\OperatingSystems;
use App\Entity\Environments;
use App\Entity\HardwareProfiles;
use Opensaucesystems\Lxd\Exception\NotFoundException;
use Opensaucesystems\Lxd\Client as LxdClient;
use Opensaucesystems\Lxd\Endpoint\Images;
use Opensaucesystems\Lxd\Endpoint\Profiles;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\LxcEvent;
use App\Message\LxcOperation;
use App\Message\EnvironmentAction;
use App\Repository\BreedsRepository;
use App\Repository\OperatingSystemsRepository;
use App\Repository\HardwareProfilesRepository;
use App\Repository\AddressesRepository;
use App\Repository\EnvironmentsRepository;
use App\Repository\InstanceTypesRepository;

class LxcManager
{
    private LoggerInterface $logger;
    private MessageBusInterface $lxcEventBus;
    private MessageBusInterface $lxcOperationBus;
    private MessageBusInterface $environmentActionBus;

//    private LxdClient $lxcService;
    private $lxcService;  /* @phpstan-ignore-line */
    private int $timeout;
    private bool $wait;

    private EntityManagerInterface $entityManager;
    private InstanceTypesRepository $itRepository;
    private BreedsRepository $breedRepository;
    private InstanceStatusesRepository $instanceStatusesRepository;
    private InstancesRepository $instanceRepository;
    private OperatingSystemsRepository $osRepository;
    private HardwareProfilesRepository $hpRepository;
    private AddressesRepository $addressRepository;
    private EnvironmentsRepository $environmentRepository;

    public function __construct( LoggerInterface $logger, EntityManagerInterface $entityManager,
//            EnvironmentManager $environmentService,
            MessageBusInterface $environmentActionBus, 
//            MessageBusInterface $awxActionBus,
            MessageBusInterface $lxcEventBus, MessageBusInterface $lxcOperationBus,
            string $lxc_timeout, string $lxc_wait, string $lxc_url)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $entityManager;

        // Repositories
        $this->itRepository = $this->entityManager->getRepository( InstanceTypes::class);
        $this->instanceStatusesRepository = $this->entityManager->getRepository(InstanceStatuses::class);
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);
        $this->osRepository = $this->entityManager->getRepository( OperatingSystems::class);
        $this->breedRepository = $this->entityManager->getRepository(Breeds::class);
        $this->hpRepository = $this->entityManager->getRepository( HardwareProfiles::class);
        $this->addressRepository = $this->entityManager->getRepository( Addresses::class);
        $this->environmentRepository = $this->entityManager->getRepository(Environments::class);
                
//        $this->environmentService = $environmentService;
        $this->environmentActionBus = $environmentActionBus;       
        $this->lxcEventBus = $lxcEventBus;
        $this->lxcOperationBus = $lxcOperationBus;
//        $this->awxActionBus = $awxActionBus;
	$this->timeout = intval($lxc_timeout);
	$this->wait = boolval($lxc_wait);

        $config = [
            'verify' => false,
            'cert' => [
                $_ENV["PROJECT_DIR"].'/client.pem',
                ''
            ]
        ];

        $guzzle = new GuzzleClient($config);
        $adapter = new GuzzleAdapter($guzzle); 
        $this->lxcService = new LxdClient($adapter, '1.0', $lxc_url);
//        $this->lxcService = new LxdClient($adapter);
//        $this->lxcService->setUrl($lxc_url);

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

    /**
     * 
     * @param string $os_alias
     * @param string $hp_name
     * @return ?int
     */
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
            $this->logger->debug("Instance type id was not found in the database for valid OS and HW profile.");
            return null;
        }

        $instance = new Instances;

        // It is New/Started by sefault
        $instance_status = $this->instanceStatusesRepository->findOneByStatus("New");
        if($instance_status){
            $instance->setStatus($instance_status);
        }
        $instance->setInstanceType($instance_type);
        $instance->setName(bin2hex(random_bytes(10)));
        $this->logger->debug("Generated Instance name: " . $instance->getName());
        $this->entityManager->persist($instance);

        // Find an address item which is NOT linked to any instance
        $address = $this->addressRepository->findOneByInstance(null);
        if (!$address) {
            $this->logger->error("Can't allocate the address");
            return null;
        }
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

    /**
     * 
     * @param string $os_alias
     * @param string $hp_name
     * @param int $env_id
     * @param bool $async
     * @return bool
     */
    public function create($os_alias, $hp_name, $env_id = -1, $async = true): bool {
        $this->logger->debug(__METHOD__);

        if ($async) {
            $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "create",
            "os" => $os_alias, "hp" => $hp_name, "env_id" => $env_id]));
            return true;
        }

        $this->logger->debug("Creating LXC object: OS: `" . $os_alias . "`, HW profile: `" . $hp_name . "`");

        $instance_id = $this->initInstance($os_alias, $hp_name);
        $instance = $this->instanceRepository->findOneById($instance_id);

        if (!$instance) {
            $this->logger->debug("Instance creation failure");
            return false;
        }

        $address = $this->addressRepository->findOneByInstance($instance);
        if (!$address) {
            $this->logger->error("Can't allocate the address");
            return false;
        }
        
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
        $name_str = $name[3];
        
        $this->logger->debug("Created object: " . $name_str);

        $this->logger->debug('Dispatching LXC event message');
        $this->lxcEventBus->dispatch(new LxcEvent(["event" => "created", "name" => $name_str]));
            
        $instance->setName($name_str);

        // Store item into the DB
//        $this->entityManager->persist($instance);
        $this->entityManager->flush();
                
        // Starting the instance
        $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "start", "name" => $name_str]));

        // Deploy test user
//        $this->awxActionBus->dispatch(new AwxAction(["action" => "deployTestUser", "name" => $name_str]));

        // Environment id has been specified, bind to it
        if ($env_id > 0) {

            $environment = $this->environmentRepository->findOneById($env_id);

            if (!$environment) {
                $this->logger->error("Environment id wasn't found " . $env_id);
                return false;
            }

            $this->logger->debug("Binding instance to the environment: `" . $environment);

//            $this->environmentService->bindInstance($environment, $instance);
            $this->environmentActionBus->dispatch(new EnvironmentAction(["action" => "bind",
                        "env_id" => $environment->getId(), "instance_name" => $name_str]));
        }

        return true;
    }

    /**
     * 
     * @param string $name
     * @param bool $force
     * @param bool $async
     * @return bool
     */
    public function start($name, $force = false, $async = true): bool {
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

    /**
     * 
     * @param string $name
     * @param bool $force
     * @param bool $async
     * @return bool
     */
    public function stop($name, $force = false, $async = true): bool {
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

    /**
     * 
     * @param string $name
     * @param bool $force
     * @param bool $async
     * @return bool
     */
    public function restart($name, $force = false, $async = true): bool {
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

    /**
     * 
     * @param string $name
     * @param bool $force
     * @return bool
     */
    public function deleteObject($name, $force = false) {
        $this->logger->debug(__METHOD__);

        if ($this->wipeInstance($name, $force)) {
            $this->logger->debug("Instance " . $name . " was deleted successfully");
        } else {
            $this->logger->debug("Instance " . $name . " deletion failure");
            return false;
        }

        if ($this->wipeObject($name, $force)) {
            $this->logger->debug("Object " . $name . " was deleted successfully");
        } else {
            $this->logger->debug("Object " . $name . " deletion failure");
            return false;
        }
        return true;
    }

    /**
     * 
     * @param string $name
     * @param bool $force
     * @param bool $async
     * @return bool
     */
    public function deleteInstance($name, $force = false, bool $async = true) {
        $this->logger->debug(__METHOD__);

        if ($async) {
            $this->lxcOperationBus->dispatch(new LxcOperation(["command" => "delete",
                        "name" => $name]));
        } else {
            return $this->deleteObject($name, $force);
        }
        return true;
    }

    /**
     * 
     * @param string $name
     * @param bool $force
     * @return bool
     */
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
            
            $this->logger->debug('Dispatching LXC event message');
            $this->lxcEventBus->dispatch(new LxcEvent(["event" => "deleted", "name" => $name]));
         
        } catch (NotFoundException $exc) {
            $this->logger->debug("LXC object `" . $name . "` does not exist!");
            $this->logger->debug($exc->getTraceAsString());
        }
        return true;
    }

    /**
     * 
     * @param string $name
     * @param bool $force
     * @return bool
     */
    public function wipeInstance($name, $force = false)
    {
        $this->logger->debug(__METHOD__);

         // look for a specific Instance 
        $instance = $this->instanceRepository->findOneByName($name);

        if (!$instance) {
            $this->logger->debug("Instance NOT found");
            return false;
        }

        if ($instance->getStatus() != "Stopped" &&
                $instance->getStatus() != "Sleeping" &&
                $instance->getStatus() != "New") {
            $this->logger->debug("Instance is NOT stopped");
            if (!$force) {
                return false;
            } else {
                $this->logger->debug("Force opiton specified");
            }
        }

        if ($instance->getEnvs()) {
            $this->logger->debug("Instance is bound to the environment");

            if (!$force) {
                return false;
            } else {
                $this->logger->debug("Force opiton specified");
            }
        }
        /*
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
         */
        
        $this->instanceRepository->remove($instance, $flush = true);
        
        return true;
    }

    /**
     * 
     * @param bool $force
     * @return bool
     */
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

            if (!empty($info)) {

                if (is_string($info['name'])) {
                    
                    if (!$this->deleteObject($info['name'], $force)) {
                    
                        $result = false;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * 
     * @param bool $force
     * @param bool $async
     * @return bool
     */
    public function deleteAllInstances($force = false, $async = true) {//: ?InstanceTypes
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

    /**
     * 
     * @param string $name
     * @return array<mixed>|null
     */
    public function getObjectInfo($name)
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
    
    /**
     * 
     * @return array<string>|null
     */
    public function getObjectList() {
        $this->logger->debug(__METHOD__);

        $objects = $this->lxcService->containers->all();

        // TODO: handle exception

        if (count($objects)) {
            return $objects;
        } else {
            return null;
        }
    }
    
    /**
     * 
     * @param array<mixed> $info
     * @return bool
     */
    public function importInstance($info): bool {
        
        if (!array_key_exists('config', $info) || !is_array($info['config'])) {
            $this->logger->debug("Invalid LXC object information structure provided.");
            return false;
        }
        
        $hp = $this->hpRepository->findOneByName(is_array($info['profiles']) ? $info['profiles'][0] : "");
        $breed = $this->breedRepository->findOneByName($info['config']['image.os']);
        $os = $this->osRepository->findOneBy(['breed' => $breed ? $breed->getId() : -1,
            'release' => $info['config']['image.release']]);
            
        $this->logger->debug("OS: ". $os . ", breed: ". $breed . ", hp: ". $hp);

        if (!$os || !$hp) {
            $this->logger->debug("Unknown OS or HW profile provided.");
            return false;
        }

        // look for the instance type
        $instance_type = $this->itRepository->findOneBy(array('os' => $os->getId(), 'hw_profile' => $hp->getId()));
        if (!$instance_type) {
            $this->logger->debug("Instance type id was not found in the database for valid OS and HW profile.");
            return false;
        }

        $address = $this->addressRepository->findOneByMac($info['config']['volatile.eth0.hwaddr']);
        if(!$address){
            $this->logger->debug("Unknown address provided.");
            return false;
        }
        
        $instance_status = $this->instanceStatusesRepository->findOneByStatus(
                is_string($info['status']) ? $info['status'] : "New");
        if(!$instance_status){
            $this->logger->debug("Instance status invalid.");
            return false;
        }
        
        $this->logger->debug("Name: `" . (is_string($info['name']) ? $info['name'] : "") .
                "`, HP: `" . $hp . "`, " . " status: `" . $instance_status .
                "`, OS: `" . $os . "`, " . "Address: `" . $address . "`");

        $instance = new Instances;
        $instance->setStatus($instance_status);
        $instance->setInstanceType($instance_type);
        $instance->setCreatedAt(new \DateTimeImmutable($info['created_at']));
        $instance->setName(is_string($info['name']) ? $info['name'] : bin2hex(random_bytes(10)));
//        $this->logger->debug("Instance name: " . $instance->getName());
        $this->entityManager->persist($instance);

        $address->setInstance($instance);

        // TODO: catch no address available exception
        // TODO: same address can be allocated to multiple new instances on a race condidion

//        $this->logger->debug("Selected address: " . $address->getIp() . ", MAC: " . $address->getMac());

        $this->entityManager->flush();
     
        
        /*
 *         $obj = $this->instanceRepository->findOneByName($info['name']);
        if (!$obj && array_key_exists('config', $info) && is_array($info['config'])) {
            $this->io->note(sprintf('Name: %s, status: %s, type: %s, os: %s, release: %s, profile: %s, MAC: %s',
                            is_string($info['name']) ? $info['name'] : "",
                            is_string($info['status']) ? $info['status'] : "",
                            is_string($info['type']) ? $info['type'] : "",
                            $info['config']['image.os'],
                            $info['config']['image.release'],
                            is_array($info['profiles']) ? $info['profiles'][0] : "",
                            $info['config']['volatile.lxcbr0.hwaddr']));
 */  
                            return true;
    }
    
    /**
     * 
     * @return array<Instances>|null
     */
    public function getInstanceList() {//: ?InstanceTypes
        $this->logger->debug(__METHOD__);

        $instances = $this->instanceRepository->findAll();

        // TODO: handle exception

        if (count($instances)) {
            return $instances;
        } else {
            return null;
        }
    }

    /**
     * 
     * @param Images $image
     * @return array<mixed>
     */
    public function getImageInfo($image)//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

	// TODO: check image existence - input validation

	return $this->lxcService->images->info($image);
    }

    /**
     * 
     * @return array<Images>|null
     */
    public function getImageList()
    {  
        $this->logger->debug(__METHOD__);

        $images = $this->lxcService->images->all();

	// TODO: handle exception

	if (count($images)) {
            return $images;
        } else {
            return null;
        }
    }

    /**
     * 
     * @param Profiles $profile
     * @return array<mixed>
     */
    public function getProfileInfo($profile)//: ?InstanceTypes
    {  
        $this->logger->debug(__METHOD__);

	// TODO: check image existence - input validation

	return $this->lxcService->profiles->info($profile);
    }

    /**
     * 
     * @return array<Profiles>|null
     */
    public function getProfileList()
    {  
        $this->logger->debug(__METHOD__);

        $profiles = $this->lxcService->profiles->all();

	// TODO: handle exception

	if (count($profiles)) {
            return $profiles;
        } else {
            return null;
        }
    }

    /**
     * 
     * @param string $name
     * @param string $status_str
     * @return bool
     */
    public function setInstanceStatus($name, $status_str): bool {

        $this->logger->debug(__METHOD__);

        $status = $this->instanceStatusesRepository->findOneByStatus($status_str);

        if (!$status) {
            $this->logger->debug('No such instance status: ' . $status_str);
            return false;
        }

        $instance = $this->instanceRepository->findOneByName($name);

        if (!$instance) {
            $this->logger->debug('No such instance!');
            return false;
        } else {

            // Special statuses for bound instances
            $target_status = $this->tweakInstanceStatus($instance->getId(), $status_str);

            if($target_status){
                $this->logger->debug('Changing instance ' . $instance . ' status to: ' . $target_status);

                $instance->setStatus($target_status);
            }
            // Store item into the DB
//	  $this->entityManager->persist($instance);
            $this->entityManager->flush();
        }

        return true;
    }

    /**
     * 
     * @param int $instance_id
     * @param string $status_str
     * @return ?InstanceStatuses
     */
    private function tweakInstanceStatus( $instance_id, $status_str): ?InstanceStatuses {

        $this->logger->debug(__METHOD__);

        $instance = $this->instanceRepository->findOneById($instance_id);

        $status = $this->instanceStatusesRepository->findOneByStatus($status_str);

        if(!$instance){
        
            $this->logger->error('Instance with ID' . $instance_id . ' was not found.');

            return null;
        }
        
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

