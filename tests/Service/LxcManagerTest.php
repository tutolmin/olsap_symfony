<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Instances;
use App\Entity\InstanceStatuses;
use App\Repository\InstancesRepository;
use App\Repository\InstanceStatusesRepository;
use App\Service\LxcManager;

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
     * @var LxcManager
     */
    private $lxdService;
    
    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->instancesRepository = $this->entityManager->getRepository(Instances::class);       
        $this->instancesStatusesRepository = $this->entityManager->getRepository(InstanceStatuses::class);
        $this->lxdService = static::getContainer()->get(LxcManager::class);        
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
    public function testObjectStop(): void {
        
        // Get Started instance status
        $instanceStatus = $this->instancesStatusesRepository->findOneByStatus('Running');
        $this->assertNotNull($instanceStatus);

        // Find Started instance 
        $instance = $this->instancesRepository->findOneBy(['status' => $instanceStatus]);
        $this->assertNotNull($instance);        
        
        // Stop LXC object
        $this->assertTrue($this->lxdService->stop($instance->getName(), $force = true, $async = false));

        // Start LXC object back
        $this->assertTrue($this->lxdService->start($instance->getName(), $force = true, $async = false));
    }

    public function testObjectStart(): void {
        
        // Get Started instance status
        $instanceStatus = $this->instancesStatusesRepository->findOneByStatus('Stopped');
        $this->assertNotNull($instanceStatus);

        // Find Started instance 
        $instance = $this->instancesRepository->findOneBy(['status' => $instanceStatus]);
        $this->assertNotNull($instance);        

        // Start LXC object
        $this->assertTrue($this->lxdService->start($instance->getName(), $force = true, $async = false));
        
        // Stop LXC object back
        $this->assertTrue($this->lxdService->stop($instance->getName(), $force = true, $async = false));
    }    
}
