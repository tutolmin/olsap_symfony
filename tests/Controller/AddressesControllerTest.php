<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Addresses;
use App\Repository\AddressesRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class AddressesControllerTest extends WebTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    
    /**
     * 
     * @var AddressesRepository
     */
    private $addressesRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
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
    
    /**
     * 
     * @depends testAddressesListIsNotEmpty
     * @param array<Addresses> $addresses
     * @return void
     */
    public function testAddressesWebPageListContainsAllRecords($addresses): void
    {
        $this->client->request('GET', '/addresses/');

        $this->assertResponseIsSuccessful();
        
        $counter = 0;
        foreach ($addresses as $t) {

            $address = $this->addressesRepository->findOneById($t);
            $this->assertNotNull($address);

            $this->assertAnySelectorTextContains('table tr td', $address->getMac());
            
            if($counter++>10){
                break;
            }
        }        
    }
    
    /**
     * 
     * @depends testAddressesListIsNotEmpty
     * @param array<Addresses> $addresses
     * @return void
     */
    public function testAddressDisplayWebPageContainsData($addresses): void {
        
        $counter = 0;
        foreach ($addresses as $t) {

            $address = $this->addressesRepository->findOneById($t);
            $this->assertNotNull($address);
            
            $this->client->request('GET', '/addresses/' . $address->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $address->getMac());
            
            if($counter++>10){
                break;
            }
        }
    }
    
    /**
     * 
     * @depends testAddressesListIsNotEmpty
     * @param array<Addresses> $addresses
     * @return void
     */
    public function testCanRemoveAllAddressesByClickingDeleteButton(array $addresses): void {

        $counter = 0;
        foreach ($addresses as $t) {

            $address = $this->addressesRepository->findOneById($t);
            $this->assertNotNull($address);
            $id = $address->getId();

            $crawler = $this->client->request('GET', '/addresses/' . $address->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_item = $this->addressesRepository->findOneById($id);
            $this->assertNull($removed_item);            
            
            if($counter++>10){
                break;
            }
        }
    }
}
