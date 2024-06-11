<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Breeds;
use App\Repository\BreedsRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class BreedsControllerTest extends WebTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    
    /**
     * 
     * @var BreedsRepository
     */
    private $breedsRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->breedsRepository = $this->entityManager->getRepository(Breeds::class);
    }

    /**
     * 
     * @return array<Breeds>
     */
    public function testBreedsListIsNotEmpty(): array {

        $breeds = $this->breedsRepository->findAll();
        $this->assertNotEmpty($breeds);
        return $breeds;
    }
    
    /**
     * 
     * @depends testBreedsListIsNotEmpty
     * @param array<Breeds> $breeds
     * @return void
     */
    public function testBreedsWebPageListContainsAllRecords($breeds): void
    {
        $this->client->request('GET', '/breeds/');

        $this->assertResponseIsSuccessful();
        
        foreach ($breeds as $t) {

            $breed = $this->breedsRepository->findOneById($t);
            $this->assertNotNull($breed);

            $this->assertAnySelectorTextContains('table tr td', $breed->getName());
        }        
    }
    
    /**
     * 
     * @depends testBreedsListIsNotEmpty
     * @param array<Breeds> $breeds
     * @return void
     */
    public function testBreedDisplayWebPageContainsData($breeds): void {
        
        foreach ($breeds as $t) {

            $breed = $this->breedsRepository->findOneById($t);
            $this->assertNotNull($breed);
            
            $this->client->request('GET', '/breeds/' . $breed->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $breed->getName());
        }
    }
    
    /**
     * 
     * @depends testBreedsListIsNotEmpty
     * @param array<Breeds> $breeds
     * @return void
     */
    public function testCanRemoveAllBreedsByClickingDeleteButton(array $breeds): void {

        foreach ($breeds as $t) {

            $breed = $this->breedsRepository->findOneById($t);
            $this->assertNotNull($breed);
            $id = $breed->getId();

            $crawler = $this->client->request('GET', '/breeds/' . $breed->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_item = $this->breedsRepository->findOneById($id);
            $this->assertNull($removed_item);            
        }
    }
}
