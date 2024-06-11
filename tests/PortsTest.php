<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Addresses;
use App\Entity\Ports;
use App\Repository\AddressesRepository;
use App\Repository\PortsRepository;

class PortsTest extends KernelTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * 
     * @var array<string, int>
     */
    private $dummy = array('number'=>7500);
    
    /**
     * 
     * @var AddressesRepository
     */
    private $addressesRepository;

    /**
     * 
     * @var PortsRepository
     */
    private $portsRepository;
    
    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->addressesRepository = $this->entityManager->getRepository(Addresses::class);
        $this->portsRepository = $this->entityManager->getRepository(Ports::class);
    }
    
    /**
     * 
     * @return array<Ports>
     */
    public function testPortsListIsNotEmpty(): array {

        $ports = $this->portsRepository->findAll();
        $this->assertNotEmpty($ports);
        
        return $ports;
    }
    
    public function testCanNotAddPortWithoutPortNumber(): void {
        
        $port = new Ports();
//        $port->setNumber($this->dummy['number']);
        
        $this->assertFalse($this->portsRepository->add($port, true));
    }
    
    /**
     * @depends testPortsListIsNotEmpty
     * @param array<Ports> $ports
     * @return void
     */
    public function testCanNotAddDuplicatePorts( array $ports): void {

        $port = $this->portsRepository->findOneById($ports[0]->getId());
        $this->assertNotNull($port);

        $new_port = new Ports();
        $new_port->setNumber($port->getNumber());

        $this->assertFalse($this->portsRepository->add($new_port, true));
    }    
    
    /**
     * @depends testPortsListIsNotEmpty
     * @return void
     */
    public function testSparePortsAreAvailable(): void {
        
        $this->assertNotEmpty($this->portsRepository->findByAddress(null));
    }
    
    /**
     * @depends testPortsListIsNotEmpty
     * @return void
     */
    public function testEachAddressHasPort(): void {

        $addresses = $this->addressesRepository->findAll();
        $this->assertNotNull($addresses);

        foreach ($addresses as $address) {
            $this->assertNotEmpty($this->portsRepository->findOneByAddress($address->getId()));
        }
    }
    
    /**
     * 
     * @return Ports
     */
    public function testCanAddDummyPort(): Ports {
        
        $port = new Ports();
        $port->setNumber($this->dummy['number']);
        $this->assertTrue($this->portsRepository->add($port, true));
        return $port;
    }
            
    /**
     * @depends testPortsListIsNotEmpty
     * @return void
     */    
    public function testCanRemoveAllPorts(): void {

        $this->portsRepository->deleteAll();
        
        $ports = $this->portsRepository->findAll();
        $this->assertEmpty($ports);  
    }          
}
