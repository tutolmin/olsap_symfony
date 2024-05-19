<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Addresses;
//use App\Entity\OperatingSystems;
use App\Repository\AddressesRepository;
//use App\Repository\OperatingSystemsRepository;

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

    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->addressesRepository = $this->entityManager->getRepository(Addresses::class);
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
}
