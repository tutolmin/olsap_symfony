<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Technologies;
use App\Entity\Domains;
use App\Repository\TechnologiesRepository;
use App\Repository\DomainsRepository;

class TechnologiesTest extends KernelTestCase
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
     * @var DomainsRepository
     */
    private $domainsRepository;
    
    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
        $this->technologiesRepository = $this->entityManager->getRepository(Technologies::class);
        $this->domainsRepository = $this->entityManager->getRepository(Domains::class);
    }
    
    public function testTechnologiesListIsNotEmpty(): void {

        $this->assertNotEmpty($this->technologiesRepository->findAll());
    }

    public function testCanNotAddTechnologyWithoutNameOrDomain(): void {
        
        $this->assertFalse($this->technologiesRepository->add(new Technologies(), true));
    }
    
    public function testCanAddAndRemoveDummyTechnology(): void {

        $domain = $this->domainsRepository->findOneBy(array());
        $this->assertNotNull($domain);

        $technology = new Technologies();
        $technology->setName($this->dummy['name']);
        $technology->setDomain($domain);
        $this->assertTrue($this->technologiesRepository->add($technology, true));
        $this->technologiesRepository->remove($technology, true);
    }

    /**
     * @depends testTechnologiesListIsNotEmpty
     * @return void
     */
    public function testCanNotAddDuplicateTechnology(): void {
                
        $technology = $this->technologiesRepository->findOneBy(array());
        $this->assertNotNull($technology);
      
        $new_tech = new Technologies();
        $new_tech->setName($technology->getName());
        $new_tech->setDomain($technology->getDomain());
        $this->assertFalse($this->technologiesRepository->add($new_tech, true));
    }
}
