<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Instances;
use App\Entity\InstanceStatuses;
use App\Repository\InstancesRepository;
use App\Repository\InstanceStatusesRepository;
use App\Service\LxcManager;
use App\Message\LxcEvent;
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

    public function testObjectStopStart(): void {

        // Get Started instance status
        $instanceStatus = $this->instancesStatusesRepository->findOneByStatus(
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
        foreach ($messages as $message) {

            $object = $this->messengerManager->parseBody($message);
            if ($object instanceof LxcEvent) {
                
                $this->assertEquals('stopped', $object->getEvent());
                $this->assertEquals($instance->getName(), $object->getName());
            }
        }

        // Start LXC object back
        $this->assertTrue($this->lxdService->start(
                        $instance->getName(), $force = true, $async = false));

        // Check object status
        $this->checkObjectStatus($instance->getName(), 'Running');
    }
}
