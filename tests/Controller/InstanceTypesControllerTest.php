<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\InstanceTypes;
use App\Repository\InstanceTypesRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class InstanceTypesControllerTest extends WebTestCase
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    
    /**
     * 
     * @var InstanceTypesRepository
     */
    private $instanceTypesRepository;

    /**
     * 
     * @var KernelBrowser
     */
    private $client;
    
    protected function setUp(): void {

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->instanceTypesRepository = $this->entityManager->getRepository(InstanceTypes::class);
    }

    /**
     * 
     * @return array<InstanceTypes>
     */
    public function testInstanceTypesListIsNotEmpty(): array {

        $instanceTypes = $this->instanceTypesRepository->findAll();
        $this->assertNotEmpty($instanceTypes);
        return $instanceTypes;
    }
    
    /**
     * 
     * @depends testInstanceTypesListIsNotEmpty
     * @param array<InstanceTypes> $instanceTypes
     * @return void
     */
    public function testInstanceTypesWebPageListContainsAllRecords($instanceTypes): void
    {
        $this->client->request('GET', '/instance/types/');

        $this->assertResponseIsSuccessful();
        
        foreach ($instanceTypes as $t) {

            $instanceType = $this->instanceTypesRepository->findOneById($t);
            $this->assertNotNull($instanceType);

            $this->assertAnySelectorTextContains('table tr td', $instanceType->getOs());
        }        
    }
    
    /**
     * 
     * @depends testInstanceTypesListIsNotEmpty
     * @param array<InstanceTypes> $instanceTypes
     * @return void
     */
    public function testInstanceTypeDisplayWebPageContainsData($instanceTypes): void {
        
        foreach ($instanceTypes as $t) {

            $instanceType = $this->instanceTypesRepository->findOneById($t);
            $this->assertNotNull($instanceType);
            
            $this->client->request('GET', '/instance/types/' . $instanceType->getId());
            $this->assertResponseIsSuccessful();

            $this->assertAnySelectorTextContains('table tr td', $instanceType->getOs());
        }
    }
}
