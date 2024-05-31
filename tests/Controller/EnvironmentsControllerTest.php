<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Environments;
use App\Repository\EnvironmentsRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class EnvironmentsControllerTest extends WebTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    
    /**
     * 
     * @var EnvironmentsRepository
     */
    private $environmentsRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->environmentsRepository = $this->entityManager->getRepository(Environments::class);
    }

    /**
     * 
     * @return array<Environments>
     */
    public function testEnvironmentsListIsNotEmpty(): array {

        $environments = $this->environmentsRepository->findAll();
        $this->assertNotEmpty($environments);
        return $environments;
    }
    
    /**
     * 
     * @depends testEnvironmentsListIsNotEmpty
     * @param array<Environments> $environments
     * @return void
     */
    public function testEnvironmentsWebPageListContainsAllRecords($environments): void
    {
        $this->client->request('GET', '/environments/');

        $this->assertResponseIsSuccessful();
        
        foreach ($environments as $t) {

            $environment = $this->environmentsRepository->findOneById($t);
            $this->assertNotNull($environment);

            $this->assertAnySelectorTextContains('table tr td', $environment->getHash());
        }        
    }
    
    /**
     * 
     * @depends testEnvironmentsListIsNotEmpty
     * @param array<Environments> $environments
     * @return void
     */
    public function testEnvironmentDisplayWebPageContainsData($environments): void {
        
        foreach ($environments as $t) {

            $environment = $this->environmentsRepository->findOneById($t);
            $this->assertNotNull($environment);
            
            $this->client->request('GET', '/environments/' . $environment->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $environment->getHash());
        }
    }
    
    /**
     * 
     * @depends testEnvironmentsListIsNotEmpty
     * @param array<Environments> $environments
     * @return void
     */
    public function testCanRemoveAllEnvironmentsByClickingDeleteButton(array $environments): void {

        foreach ($environments as $t) {

            $environment = $this->environmentsRepository->findOneById($t);
            $this->assertNotNull($environment);
            $id = $environment->getId();

            $crawler = $this->client->request('GET', '/environments/' . $environment->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_environment = $this->environmentsRepository->findOneById($id);
            $this->assertNull($removed_environment);            
        }
    }
}
