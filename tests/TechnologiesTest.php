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

//        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->technologiesRepository = $this->entityManager->getRepository(Technologies::class);
        $this->domainsRepository = $this->entityManager->getRepository(Domains::class);
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

    public function testCanNotAddTechnologyWithoutMandatoryFields(): void {
        
        $this->assertFalse($this->technologiesRepository->add(new Technologies(), true));
    }
    
    public function testCanAddDummyTechnology(): void {

        $domain = $this->domainsRepository->findOneBy(array());
        $this->assertNotNull($domain);

        $technology = new Technologies();
        $technology->setName($this->dummy['name']);
        $technology->setDomain($domain);
        $this->assertTrue($this->technologiesRepository->add($technology, true));
    }

    /**
     * @depends testTechnologiesListIsNotEmpty
     * @param array<Technologies> $techs
     * @return void
     */
    public function testCanRemoveRandomTechnology( array $techs): void {

        $tech = $this->technologiesRepository->findOneById($techs[0]->getId());
        $this->assertNotNull($tech);
        $id = $tech->getId();
    
        $this->technologiesRepository->remove($tech, true);
        
        $removed_tech = $this->technologiesRepository->findOneById($id);
        $this->assertNull($removed_tech);
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
