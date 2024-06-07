<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\OperatingSystems;
use App\Repository\OperatingSystemsRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class OperatingSystemsControllerTest extends WebTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    
    /**
     * 
     * @var OperatingSystemsRepository
     */
    private $operatingSystemsRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->operatingSystemsRepository = $this->entityManager->getRepository(OperatingSystems::class);
    }

    /**
     * 
     * @return array<OperatingSystems>
     */
    public function testOperatingSystemsListIsNotEmpty(): array {

        $operatingSystems = $this->operatingSystemsRepository->findAll();
        $this->assertNotEmpty($operatingSystems);
        return $operatingSystems;
    }
    
    /**
     * 
     * @depends testOperatingSystemsListIsNotEmpty
     * @param array<OperatingSystems> $operatingSystems
     * @return void
     */
    public function testOperatingSystemsWebPageListContainsAllRecords($operatingSystems): void
    {
        $this->client->request('GET', '/operating/systems/');

        $this->assertResponseIsSuccessful();
        
        foreach ($operatingSystems as $t) {

            $operatingSystem = $this->operatingSystemsRepository->findOneById($t);
            $this->assertNotNull($operatingSystem);

            $this->assertAnySelectorTextContains('table tr td', $operatingSystem->getRelease());
        }        
    }
    
    /**
     * 
     * @depends testOperatingSystemsListIsNotEmpty
     * @param array<OperatingSystems> $operatingSystems
     * @return void
     */
    public function testOperatingSystemDisplayWebPageContainsData($operatingSystems): void {
        
        foreach ($operatingSystems as $t) {

            $operatingSystem = $this->operatingSystemsRepository->findOneById($t);
            $this->assertNotNull($operatingSystem);
            
            $this->client->request('GET', '/operating/systems/' . $operatingSystem->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $operatingSystem->getRelease());
        }
    }
    
    /**
     * 
     * @depends testOperatingSystemsListIsNotEmpty
     * @param array<OperatingSystems> $operatingSystems
     * @return void
     */
    public function testCanRemoveAllOperatingSystemsByClickingDeleteButton(array $operatingSystems): void {

        foreach ($operatingSystems as $t) {

            $operatingSystem = $this->operatingSystemsRepository->findOneById($t);
            $this->assertNotNull($operatingSystem);
            $id = $operatingSystem->getId();

            $crawler = $this->client->request('GET', '/operating/systems/' . $operatingSystem->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_operatingSystem = $this->operatingSystemsRepository->findOneById($id);
            $this->assertNull($removed_operatingSystem);            
        }
    }
}
