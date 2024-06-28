<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Instances;
use App\Entity\InstanceStatuses;
use App\Entity\InstanceTypes;
use App\Repository\InstancesRepository;
use App\Repository\InstanceTypesRepository;
use App\Repository\InstanceStatusesRepository;
use App\Service\LxcManager;
use App\Message\LxcEvent;
use App\Message\LxcOperation;
use App\Entity\MessengerMessages;
use App\Repository\MessengerMessagesRepository;
use App\Service\MessengerMessagesManager;

class LxcManagerTest extends KernelTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * 
     * @var InstancesRepository
     */
    private $instancesRepository;
    
    /**
     * 
     * @var InstanceStatusesRepository
     */
    private $instancesStatusesRepository;
    
    /**
     * 
     * @var InstanceTypesRepository
     */
    private $instanceTypesRepository;

    private MessengerMessagesRepository $messengerRepository;
     
    private MessengerMessagesManager $messengerManager;
       
    /**
     * 
     * @var LxcManager
     */
    private $lxdService;
    
    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->instancesRepository = $this->entityManager->getRepository(Instances::class);       
        $this->instancesStatusesRepository = $this->entityManager->getRepository(InstanceStatuses::class);
        $this->instanceTypesRepository = $this->entityManager->getRepository(InstanceTypes::class);
        $this->messengerRepository = $this->entityManager->getRepository(MessengerMessages::class);
        $this->lxdService = static::getContainer()->get(LxcManager::class);        
        $this->messengerManager = static::getContainer()->get(MessengerMessagesManager::class);
    }   
/*    
    protected function tearDown(): void {
        
    }
    
    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        // $routerService = static::getContainer()->get('router');
        // $myCustomService = static::getContainer()->get(CustomService::class);
    }

 * 
 */ 
    /**
     * 
     * @param string $name
     * @param string $status
     * @return void
     */
    private function checkObjectStatus(string $name, string $status): void {

        // Check object status
        $object = $this->lxdService->getObjectInfo($name);
        $this->assertNotNull($object);
        $this->assertEquals($status, $object['status']);
    }
    
    
    
    public function testObjectRestart(): void {

        // Get Started instance status
        $instanceStatus = $this->instancesStatusesRepository->findByStatus(
                ['Running','Started']);
        $this->assertNotNull($instanceStatus);

        // Find Started instance 
        $instance = $this->instancesRepository->findOneBy(
                ['status' => $instanceStatus]);
        $this->assertNotNull($instance);

        // Check object status
        $this->checkObjectStatus($instance->getName(), 'Running');

        // Stop LXC object
        $this->assertTrue($this->lxdService->restart(
                        $instance->getName(), $force = true, $async = false));

        // Check object status
        $this->checkObjectStatus($instance->getName(), 'Running'); 

        // Check Messenger message of Instance stop type
        $messages = $this->messengerRepository->findAll();
        $this->assertNotEmpty($messages);
        
        // Iterate through all the messages
        $counter = 0;
        foreach ($messages as $message) {

            $object = $this->messengerManager->parseBody($message);
            if ($object instanceof LxcEvent) {
                
                $this->assertEquals('started', $object->getEvent());
                $this->assertEquals($instance->getName(), $object->getName());
                $counter++;
            }
        }
        $this->assertEquals(1, $counter);        
    }
        
    
    
    public function testObjectStopStart(): void {

        // Get Started instance status
        $instanceStatus = $this->instancesStatusesRepository->findByStatus(
                ['Running','Started']);
        $this->assertNotNull($instanceStatus);

        // Find Started instance 
        $instance = $this->instancesRepository->findOneBy(
                ['status' => $instanceStatus]);
        $this->assertNotNull($instance);

        // Check object status
        $this->checkObjectStatus($instance->getName(), 'Running');

        // Stop LXC object
        $this->assertTrue($this->lxdService->stop(
                        $instance->getName(), $force = true, $async = false));

        // Check object status
        $this->checkObjectStatus($instance->getName(), 'Stopped');

        // Check Messenger message of Instance stop type
        $messages = $this->messengerRepository->findAll();
        $this->assertNotEmpty($messages);

        // Iterate through all the messages
        $counter = 0;
        foreach ($messages as $message) {

            $object = $this->messengerManager->parseBody($message);
            if ($object instanceof LxcEvent) {
                
                $this->assertEquals('stopped', $object->getEvent());
                $this->assertEquals($instance->getName(), $object->getName());
                $counter++;
            }
        }
        $this->assertEquals(1, $counter);

        // Start LXC object back
        $this->assertTrue($this->lxdService->start(
                        $instance->getName(), $force = true, $async = false));

        // Check object status
        $this->checkObjectStatus($instance->getName(), 'Running');
    }
     
    
    
    public function testObjectCreate(): void {

        // Get supported instance type
        $instance_type = $this->instanceTypesRepository->findOneBy([]);
        $this->assertNotNull($instance_type);
        
        // Create an LXC object
        $this->assertTrue($this->lxdService->create(
                $instance_type->getOs()->getAlias() ?? '', 
                $instance_type->getHwProfile()->getName(), 
                -1, 
                $async = false));
        
        // Check Messenger message of Instance stop type
        $messages = $this->messengerRepository->findAll();
        $this->assertNotEmpty($messages);

        // Iterate through all the messages
        $counter = 0;
        $name = "";
        foreach ($messages as $message) {

            $object = $this->messengerManager->parseBody($message);
            if ($object instanceof LxcEvent) {
                
                $this->assertEquals('created', $object->getEvent());
                $name = $object->getName();
                $counter++;
                
                // Delete message
                $this->messengerRepository->remove($message, true);
            }
            if ($object instanceof LxcOperation) {
                
                $this->assertEquals('start', $object->getCommand());
                $counter++;
            }            
        }
        $this->assertEquals(2, $counter);        

        // Delete LXC object back
        $this->assertTrue($this->lxdService->wipeObject(
                        $name, $force = true));
        
        // Check Messenger message of Instance stop type
        $messages = $this->messengerRepository->findAll();
        $this->assertNotEmpty($messages);

        // Iterate through all the messages
        $counter = 0;
        foreach ($messages as $message) {

            $object = $this->messengerManager->parseBody($message);
            if ($object instanceof LxcEvent) {
                
                $this->assertEquals('deleted', $object->getEvent());
                $this->assertEquals($name, $object->getName());
                $counter++;
            }
        }
        $this->assertEquals(1, $counter);
        
    }
}
