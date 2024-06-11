<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Testees;
use App\Repository\TesteesRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class TesteesControllerTest extends WebTestCase
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
    private $dummy = array('email'=>'dummy@dummy.net', 'oauth_token'=>'dummy');
       
    /**
     * 
     * @var TesteesRepository
     */
    private $testeesRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->testeesRepository = $this->entityManager->getRepository(Testees::class);
    }

    /**
     * 
     * @return array<Testees>
     */
    public function testTesteesListIsNotEmpty(): array {

        $testees = $this->testeesRepository->findAll();
        $this->assertNotEmpty($testees);
        return $testees;
    }
    
    /**
     * 
     * @depends testTesteesListIsNotEmpty
     * @param array<Testees> $testees
     * @return void
     */
    public function testTesteesWebPageListContainsAllRecords($testees): void
    {
        $this->client->request('GET', '/testees/');

        $this->assertResponseIsSuccessful();
        
        foreach ($testees as $t) {

            $testee = $this->testeesRepository->findOneById($t);
            $this->assertNotNull($testee);

            $this->assertAnySelectorTextContains('table tr td', $testee->getEmail());
        }        
    }
    
    /**
     * 
     * @depends testTesteesListIsNotEmpty
     * @param array<Testees> $testees
     * @return void
     */
    public function testTesteeDisplayWebPageContainsData($testees): void {
        
        foreach ($testees as $t) {

            $testee = $this->testeesRepository->findOneById($t);
            $this->assertNotNull($testee);
            
            $this->client->request('GET', '/testees/' . $testee->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $testee->getOauthToken());
        }
    }
    
    /**
     * 
     * @depends testTesteesListIsNotEmpty
     * @param array<Testees> $testees
     * @return void
     */
    public function testCanRemoveAllTesteesByClickingDeleteButton(array $testees): void {

        foreach ($testees as $t) {

            $testee = $this->testeesRepository->findOneById($t);
            $this->assertNotNull($testee);
            $id = $testee->getId();

            $crawler = $this->client->request('GET', '/testees/' . $testee->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_testee = $this->testeesRepository->findOneById($id);
            $this->assertNull($removed_testee);            
        }
    }
    
    /**
     * 
     * @return void
     */
    public function testCanAddDummyTesteeBySubmittingForm(): void {

        $crawler = $this->client->request('GET', '/testees/new');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Save');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['testees[email]'] = $this->dummy['email'];
        $form['testees[oauth_token]'] = $this->dummy['oauth_token'];
        $registeredAt = new \DateTimeImmutable('now');
        $form['testees[registered_at]'] = $registeredAt->format('Y-m-d H:i:s');

        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->testeesRepository->findOneByEmail($this->dummy['email']);
        $this->assertNotNull($item);        
    }   
}
