<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Addresses;
use App\Entity\Instances;
use App\Repository\AddressesRepository;
use App\Repository\InstancesRepository;
use App\Service\AddressesManager;

class AddressesTest extends KernelTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * 
     * @var array<string>
     */
    private $dummy = array('ip'=>'192.168.0.1', 'mac'=>'aa:bb:cc:dd:ee:ff');
    
    /**
     * 
     * @var AddressesRepository
     */
    private $addressesRepository;

    /**
     * 
     * @var InstancesRepository
     */
    private $instanceRepository;
    
    /**
     * 
     * @var AddressesManager
     */
    private $addressManager;
        
    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->addressesRepository = $this->entityManager->getRepository(Addresses::class);
        $this->instanceRepository = $this->entityManager->getRepository(Instances::class);
        $this->addressManager = static::getContainer()->get(AddressesManager::class);        
    }
    
    /**
     * 
     * @return array<Addresses>
     */
    public function testAddressesListIsNotEmpty(): array {

        $addresses = $this->addressesRepository->findAll();
        $this->assertNotEmpty($addresses);
        
        return $addresses;
    }
    
    public function testCanNotAddAddressWithoutIp(): void {
        
        $address = new Addresses();
        $address->setMac($this->dummy['mac']);
        
        $this->assertFalse($this->addressesRepository->add($address, true));
    }
    
    public function testCanNotAddAddressWithoutMac(): void {
        
        $address = new Addresses();
        $address->setIp($this->dummy['ip']);
        
        $this->assertFalse($this->addressesRepository->add($address, true));
    }

    /**
     * @depends testAddressesListIsNotEmpty
     * @param array<Addresses> $addresses
     * @return void
     */
    public function testCanNotAddDuplicateAddresses( array $addresses): void {

        $address = $this->addressesRepository->findOneById($addresses[0]->getId());
        $this->assertNotNull($address);

        $new_address = new Addresses();
        $new_address->setIp($address->getIp());
        $new_address->setMac($address->getMac());

        $this->assertFalse($this->addressesRepository->add($new_address, true));
    }    
    
    /**
     * @depends testAddressesListIsNotEmpty
     * @return void
     */
    public function testSpareAddressesAreAvailable(): void {
        
        $this->assertNotEmpty($this->addressesRepository->findByInstance(null));
    }
    
    /**
     * @depends testAddressesListIsNotEmpty
     * @return void
     */
    public function testEachInstanceHasAddress(): void {

        $instances = $this->instanceRepository->findAll();
        $this->assertNotNull($instances);

        foreach ($instances as $instance) {
            $this->assertNotEmpty($this->addressesRepository->findOneByInstance($instance->getId()));
        }
    }
    
    /**
     * 
     * @return Addresses
     */
    public function testCanAddDummyAddress(): Addresses {
        
        $address = new Addresses();
        $address->setIp($this->dummy['ip']);
        $address->setMac($this->dummy['mac']);
        $this->assertTrue($this->addressesRepository->add($address, true));
        return $address;
    }

    /**
     * @depends testCanAddDummyAddress
     * @param Addresses $address
     * @return void
     */
            
    /**
     * @depends testAddressesListIsNotEmpty
     * @param array<Addresses> $addresses
     * @return void
     */    
    public function testCanRemoveAllAddresses($addresses): void {

        $counter = 0;
        foreach ($addresses as $a) {

            $item = $this->addressesRepository->findOneById($a);
            $this->assertNotNull($item);
            $id = $item->getId();

            $this->addressManager->removeAddress($item);

            $removed_item = $this->addressesRepository->findOneById($id);
            $this->assertNull($removed_item);
              
            if($counter++>10){
                break;
            }
        }

        $this->assertEmpty($this->addressesRepository->findAll());

        // No instances left
//        $this->assertEmpty($this->instanceRepository->findAll());
    }          
}
