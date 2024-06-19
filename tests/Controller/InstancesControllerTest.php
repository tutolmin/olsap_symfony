<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Instances;
use App\Repository\InstancesRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class InstancesControllerTest extends WebTestCase
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
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->instancesRepository = $this->entityManager->getRepository(Instances::class);
    }

    /**
     * 
     * @return array<Instances>
     */
    public function testInstancesListIsNotEmpty(): array {

        $instances = $this->instancesRepository->findAll();
        $this->assertNotEmpty($instances);
        return $instances;
    }
    
    /**
     * 
     * @depends testInstancesListIsNotEmpty
     * @param array<Instances> $instances
     * @return void
     */
    public function testInstancesWebPageListContainsAllRecords($instances): void
    {
        $this->client->request('GET', '/instances/');

        $this->assertResponseIsSuccessful();
        
        foreach ($instances as $t) {

            $instance = $this->instancesRepository->findOneById($t);
            $this->assertNotNull($instance);

            $this->assertAnySelectorTextContains('table tr td', $instance->getName());
        }        
    }
    
    /**
     * 
     * @depends testInstancesListIsNotEmpty
     * @param array<Instances> $instances
     * @return void
     */
    public function testInstanceDisplayWebPageContainsData($instances): void {
        
        foreach ($instances as $t) {

            $instance = $this->instancesRepository->findOneById($t);
            $this->assertNotNull($instance);
            
            $this->client->request('GET', '/instances/' . $instance->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $instance->getName());
        }
    }
    
    /**
     * 
     * @depends testInstancesListIsNotEmpty
     * @param array<Instances> $instances
     * @return void
     */
    public function testDeleteButtonDoesNotDeleteInstanceInstantly(array $instances): void {

        foreach ($instances as $t) {

            $instance = $this->instancesRepository->findOneById($t);
            $this->assertNotNull($instance);
            $id = $instance->getId();

            $crawler = $this->client->request('GET', '/instances/' . $instance->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_instance = $this->instancesRepository->findOneById($id);
            $this->assertNotNull($removed_instance);            
        }
    }   
}
