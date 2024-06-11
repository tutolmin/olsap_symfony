<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Ports;
use App\Repository\PortsRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class PortsControllerTest extends WebTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    
    /**
     * 
     * @var PortsRepository
     */
    private $portsRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
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
    
    /**
     * 
     * @depends testPortsListIsNotEmpty
     * @param array<Ports> $ports
     * @return void
     */
    public function testPortsWebPageListContains10Records($ports): void
    {
        $this->client->request('GET', '/ports/');

        $this->assertResponseIsSuccessful();
        
        $counter = 0;
        foreach ($ports as $t) {

            $port = $this->portsRepository->findOneById($t);
            $this->assertNotNull($port);

            $this->assertAnySelectorTextContains('table tr td', $port);
            
            if($counter++>10){
                break;
            }
        }        
    }
    
    /**
     * 
     * @depends testPortsListIsNotEmpty
     * @param array<Ports> $ports
     * @return void
     */
    public function testPortDisplayWebPageContainsData($ports): void {
        
        $counter = 0;
        foreach ($ports as $t) {

            $port = $this->portsRepository->findOneById($t);
            $this->assertNotNull($port);
            
            $this->client->request('GET', '/ports/' . $port->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $port);
            
            if($counter++>10){
                break;
            }
        }
    }
    
    /**
     * 
     * @depends testPortsListIsNotEmpty
     * @param array<Ports> $ports
     * @return void
     */
    public function testCanRemove10PortsByClickingDeleteButton(array $ports): void {

        $counter = 0;        
        foreach ($ports as $t) {

            $port = $this->portsRepository->findOneById($t);
            $this->assertNotNull($port);
            $id = $port->getId();

            $crawler = $this->client->request('GET', '/ports/' . $port->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_item = $this->portsRepository->findOneById($id);
            $this->assertNull($removed_item);            
            
            if($counter++>10){
                break;
            }
        }
    }
}
