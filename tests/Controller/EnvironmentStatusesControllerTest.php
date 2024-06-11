<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\EnvironmentStatuses;
use App\Repository\EnvironmentStatusesRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class EnvironmentStatusesControllerTest extends WebTestCase
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
     * @var EnvironmentStatusesRepository
     */
    private $environmentStatusesRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->environmentStatusesRepository = $this->entityManager->getRepository(EnvironmentStatuses::class);
    }

    /**
     * 
     * @return array<EnvironmentStatuses>
     */
    public function testEnvironmentStatusesListIsNotEmpty(): array {

        $environmentStatuses = $this->environmentStatusesRepository->findAll();
        $this->assertNotEmpty($environmentStatuses);
        return $environmentStatuses;
    }
    
    /**
     * 
     * @depends testEnvironmentStatusesListIsNotEmpty
     * @param array<EnvironmentStatuses> $environmentStatuses
     * @return void
     */
    public function testEnvironmentStatusesWebPageListContainsAllRecords($environmentStatuses): void
    {
        $this->client->request('GET', '/environment/statuses/');

        $this->assertResponseIsSuccessful();
        
        foreach ($environmentStatuses as $t) {

            $environment = $this->environmentStatusesRepository->findOneById($t);
            $this->assertNotNull($environment);

            $this->assertAnySelectorTextContains('table tr td', $environment->getStatus());
        }        
    }
    
    /**
     * 
     * @depends testEnvironmentStatusesListIsNotEmpty
     * @param array<EnvironmentStatuses> $environmentStatuses
     * @return void
     */
    public function testEnvironmentStatusDisplayWebPageContainsData($environmentStatuses): void {
        
        foreach ($environmentStatuses as $t) {

            $environment = $this->environmentStatusesRepository->findOneById($t);
            $this->assertNotNull($environment);
            
            $this->client->request('GET', '/environment/statuses/' . $environment->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $environment->getStatus());
        }
    }
    
    /**
     * 
     * @depends testEnvironmentStatusesListIsNotEmpty
     * @param array<EnvironmentStatuses> $environmentStatuses
     * @return void
     */
    public function testCanRemoveAllEnvironmentStatusesByClickingDeleteButton(array $environmentStatuses): void {

        foreach ($environmentStatuses as $t) {

            $item = $this->environmentStatusesRepository->findOneById($t);
            $this->assertNotNull($item);
            $id = $item->getId();

            $crawler = $this->client->request('GET', '/environment/statuses/' . $item->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_item = $this->environmentStatusesRepository->findOneById($id);
            $this->assertNull($removed_item);            
        }
    }
    
    /**
     * 
     * @return void
     */
    public function testCanAddDummyEnvironmentStatusBySubmittingForm(): void {

        $crawler = $this->client->request('GET', '/environment/statuses/new');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Save');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['environment_statuses[status]'] = $this->dummy['name'];

        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->environmentStatusesRepository->findOneByStatus($this->dummy['name']);
        $this->assertNotNull($item);        
    }
}
