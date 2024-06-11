<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\InstanceStatuses;
use App\Repository\InstanceStatusesRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class InstanceStatusesControllerTest extends WebTestCase
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
    private $dummy = array('name'=>'Dummy');
        
    /**
     * 
     * @var InstanceStatusesRepository
     */
    private $instanceStatusesRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->instanceStatusesRepository = $this->entityManager->getRepository(InstanceStatuses::class);
    }

    /**
     * 
     * @return array<InstanceStatuses>
     */
    public function testInstanceStatusesListIsNotEmpty(): array {

        $instanceStatuses = $this->instanceStatusesRepository->findAll();
        $this->assertNotEmpty($instanceStatuses);
        return $instanceStatuses;
    }
    
    /**
     * 
     * @depends testInstanceStatusesListIsNotEmpty
     * @param array<InstanceStatuses> $instanceStatuses
     * @return void
     */
    public function testInstanceStatusesWebPageListContainsAllRecords($instanceStatuses): void
    {
        $this->client->request('GET', '/instance/statuses/');

        $this->assertResponseIsSuccessful();
        
        foreach ($instanceStatuses as $t) {

            $instance = $this->instanceStatusesRepository->findOneById($t);
            $this->assertNotNull($instance);

            $this->assertAnySelectorTextContains('table tr td', $instance->getStatus());
        }        
    }
    
    /**
     * 
     * @depends testInstanceStatusesListIsNotEmpty
     * @param array<InstanceStatuses> $instanceStatuses
     * @return void
     */
    public function testInstanceStatusDisplayWebPageContainsData($instanceStatuses): void {
        
        foreach ($instanceStatuses as $t) {

            $instance = $this->instanceStatusesRepository->findOneById($t);
            $this->assertNotNull($instance);
            
            $this->client->request('GET', '/instance/statuses/' . $instance->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $instance->getStatus());
        }
    }
    
    /**
     * 
     * @depends testInstanceStatusesListIsNotEmpty
     * @param array<InstanceStatuses> $instanceStatuses
     * @return void
     */
    public function testCanRemoveAllInstanceStatusesByClickingDeleteButton(array $instanceStatuses): void {

        foreach ($instanceStatuses as $t) {

            $item = $this->instanceStatusesRepository->findOneById($t);
            $this->assertNotNull($item);
            $id = $item->getId();

            $crawler = $this->client->request('GET', '/instance/statuses/' . $item->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_item = $this->instanceStatusesRepository->findOneById($id);
            $this->assertNull($removed_item);           
        }
    }
    
    /**
     * 
     * @return void
     */
    public function testCanAddDummyInstanceStatusBySubmittingForm(): void {

        $crawler = $this->client->request('GET', '/instance/statuses/new');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Save');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['instance_statuses[status]'] = $this->dummy['name'];

        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->instanceStatusesRepository->findOneByStatus($this->dummy['name']);
        $this->assertNotNull($item);        
    }
}
