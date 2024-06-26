<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\HardwareProfiles;
use App\Repository\HardwareProfilesRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class HardwareProfilesControllerTest extends WebTestCase
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
    private $dummy = array('name' => 'Dummy');
  
    /**
     * 
     * @var HardwareProfilesRepository
     */
    private $hardwareProfilesRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->hardwareProfilesRepository = $this->entityManager->getRepository(HardwareProfiles::class);
    }

    /**
     * 
     * @return array<HardwareProfiles>
     */
    public function testHardwareProfilesListIsNotEmpty(): array {

        $hardwareProfiles = $this->hardwareProfilesRepository->findAll();
        $this->assertNotEmpty($hardwareProfiles);
        return $hardwareProfiles;
    }
    
    /**
     * 
     * @depends testHardwareProfilesListIsNotEmpty
     * @param array<HardwareProfiles> $hardwareProfiles
     * @return void
     */
    public function testHardwareProfilesWebPageListContainsAllRecords($hardwareProfiles): void
    {
        $this->client->request('GET', '/hardware/profiles/');

        $this->assertResponseIsSuccessful();
        
        foreach ($hardwareProfiles as $t) {

            $tech = $this->hardwareProfilesRepository->findOneById($t);
            $this->assertNotNull($tech);

            $this->assertAnySelectorTextContains('table tr td', $tech->getName());
        }        
    }
    
    /**
     * 
     * @depends testHardwareProfilesListIsNotEmpty
     * @param array<HardwareProfiles> $hardwareProfiles
     * @return void
     */
    public function testHardwareProfileDisplayWebPageContainsData($hardwareProfiles): void {
        
        foreach ($hardwareProfiles as $t) {

            $tech = $this->hardwareProfilesRepository->findOneById($t);
            $this->assertNotNull($tech);
            
            $this->client->request('GET', '/hardware/profiles/' . $tech->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $tech->getName());
        }
    }
    
    /**
     * 
     * @depends testHardwareProfilesListIsNotEmpty
     * @param array<HardwareProfiles> $hardwareProfiles
     * @return void
     */
    public function testCanRemoveAllHardwareProfilesByClickingDeleteButton(array $hardwareProfiles): void {

        foreach ($hardwareProfiles as $t) {

            $hardwareProfile = $this->hardwareProfilesRepository->findOneById($t);
            $this->assertNotNull($hardwareProfile);
            $id = $hardwareProfile->getId();

            $crawler = $this->client->request('GET', '/hardware/profiles/' . $hardwareProfile->getId());
            $this->assertResponseIsSuccessful();

            // select the button
            $buttonCrawlerNode = $crawler->selectButton('Delete');

            // retrieve the Form object for the form belonging to this button
            $form = $buttonCrawlerNode->form();

            // submit the Form object
            $this->client->submit($form);
            
            $removed_item = $this->hardwareProfilesRepository->findOneById($id);
            $this->assertNull($removed_item);            
        }
    }
    
    /**
     * 
     * @return void
     */
    public function testCanAddDummyHardwareProfileBySubmittingForm(): void {

        $crawler = $this->client->request('GET', '/hardware/profiles/new');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Save');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['hardware_profiles[name]'] = $this->dummy['name'];
        $form['hardware_profiles[supported]'] = '1';
        $form['hardware_profiles[type]'] = '1';
        
        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->hardwareProfilesRepository->findOneByName($this->dummy['name']);
        $this->assertNotNull($item);        
    } 
    
    /**
     * @depends testHardwareProfilesListIsNotEmpty
     * @param array<HardwareProfiles> $hardwareProfiles
     * @return void
     */
    public function testCanEditHardwareProfileBySubmittingForm($hardwareProfiles): void {

        $operatingSystem = $this->hardwareProfilesRepository->findOneById($hardwareProfiles[0]);
        $this->assertNotNull($operatingSystem);
            
        $crawler = $this->client->request('GET', '/hardware/profiles/'.$operatingSystem->getId().'/edit');

        // select the button
        $buttonCrawlerNode = $crawler->selectButton('Update');

        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // set values on a form object
        $form['hardware_profiles[name]'] = $this->dummy['name'];
        
        // submit the Form object
        $this->client->submit($form);
        
        $item = $this->hardwareProfilesRepository->findOneByName($this->dummy['name']);
        $this->assertNotNull($item);        
    }     
}
