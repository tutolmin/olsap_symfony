<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Technologies;
use App\Repository\TechnologiesRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class TechnologiesControllerTest extends WebTestCase
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
     * @var TechnologiesRepository
     */
    private $technologiesRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->technologiesRepository = $this->entityManager->getRepository(Technologies::class);
    }

    /**
     * 
     * @return array<Technologies>
     */
    public function testTechnologiesListIsNotEmpty(): array {

        $techs = $this->technologiesRepository->findAll();
        $this->assertNotEmpty($techs);
        return $techs;
    }
    
    /**
     * 
     * @depends testTechnologiesListIsNotEmpty
     * @param array<Technologies> $techs
     * @return void
     */
    public function testTechnologiesWebPageListContainsAllRecords($techs): void
    {
        $this->client->request('GET', '/technologies/');

        $this->assertResponseIsSuccessful();
        
        foreach ($techs as $t) {

            $tech = $this->technologiesRepository->findOneById($t);
            $this->assertNotNull($tech);

            $this->assertAnySelectorTextContains('table tr td', $tech->getName());
        }        
    }
    
    /**
     * 
     * @depends testTechnologiesListIsNotEmpty
     * @param array<Technologies> $techs
     * @return void
     */
    public function testTechnologyDisplayWebPageContainsData($techs): void {
        
        foreach ($techs as $t) {

            $tech = $this->technologiesRepository->findOneById($t);
            $this->assertNotNull($tech);
            
            $this->client->request('GET', '/technologies/' . $tech->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $tech->getDomain());
        }
    }
    
    /**
     * 
     * @depends testTechnologiesListIsNotEmpty
     * @param array<Technologies> $techs
     * @return void
     */
    public function testCanRemoveAllTechnologiesByClickingDeleteButton(array $techs): void {

        foreach ($techs as $t) {

            $tech = $this->technologiesRepository->findOneById($t);
            $this->assertNotNull($tech);
            $id = $tech->getId();

            $crawler = $this->client->request('GET', '/technologies/' . $tech->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_testee = $this->technologiesRepository->findOneById($id);
            $this->assertNull($removed_testee);            
        }
    }
    
    /**
     * 
     * @return void
     */
    public function testCanAddDummyTechnologyBySubmittingForm(): void {

        $crawler = $this->client->request('GET', '/technologies/new');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Save');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['technologies[name]'] = $this->dummy['name'];
        
        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->technologiesRepository->findOneByName($this->dummy['name']);
        $this->assertNotNull($item);        
    } 
}
