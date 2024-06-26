<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Domains;
use App\Repository\DomainsRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class DomainsControllerTest extends WebTestCase
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
     * @var DomainsRepository
     */
    private $domainsRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->domainsRepository = $this->entityManager->getRepository(Domains::class);
    }

    /**
     * 
     * @return array<Domains>
     */
    public function testDomainsListIsNotEmpty(): array {

        $domains = $this->domainsRepository->findAll();
        $this->assertNotEmpty($domains);
        return $domains;
    }
    
    /**
     * 
     * @depends testDomainsListIsNotEmpty
     * @param array<Domains> $domains
     * @return void
     */
    public function testDomainsWebPageListContainsAllRecords($domains): void
    {
        $this->client->request('GET', '/domains/');

        $this->assertResponseIsSuccessful();
        
        foreach ($domains as $t) {

            $tech = $this->domainsRepository->findOneById($t);
            $this->assertNotNull($tech);

            $this->assertAnySelectorTextContains('table tr td', $tech->getName());
        }        
    }
    
    /**
     * 
     * @depends testDomainsListIsNotEmpty
     * @param array<Domains> $domains
     * @return void
     */
    public function testDomainDisplayWebPageContainsData($domains): void {
        
        foreach ($domains as $t) {

            $tech = $this->domainsRepository->findOneById($t);
            $this->assertNotNull($tech);
            
            $this->client->request('GET', '/domains/' . $tech->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $tech->getName());
        }
    }
    
    /**
     * 
     * @depends testDomainsListIsNotEmpty
     * @param array<Domains> $domains
     * @return void
     */
    public function testCanRemoveAllDomainsByClickingDeleteButton(array $domains): void {

        foreach ($domains as $t) {

            $domain = $this->domainsRepository->findOneById($t);
            $this->assertNotNull($domain);
            $id = $domain->getId();

            $crawler = $this->client->request('GET', '/domains/' . $domain->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_item = $this->domainsRepository->findOneById($id);
            $this->assertNull($removed_item);            
        }
    }
    
    /**
     * 
     * @return void
     */
    public function testCanAddDummyDomainBySubmittingForm(): void {

        $crawler = $this->client->request('GET', '/domains/new');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Save');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['domains[name]'] = $this->dummy['name'];

        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->domainsRepository->findOneByName($this->dummy['name']);
        $this->assertNotNull($item);        
    }
    
    /**
     * @depends testDomainsListIsNotEmpty
     * @param array<Domains> $domains
     * @return void
     */
    public function testCanEditDomainBySubmittingForm($domains): void {

        $domain = $this->domainsRepository->findOneById($domains[0]);
        $this->assertNotNull($domain);
            
        $crawler = $this->client->request('GET', '/domains/'.$domain->getId().'/edit');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Update');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['domains[name]'] = $this->dummy['name'];

        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->domainsRepository->findOneByName($this->dummy['name']);
        $this->assertNotNull($item);        
    }
}
