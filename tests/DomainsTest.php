<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Domains;
use App\Entity\Technologies;
use App\Repository\DomainsRepository;
use App\Repository\TechnologiesRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class DomainsTest extends KernelTestCase
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
     * @var TechnologiesRepository
     */
    private $technologiesRepository;
    
    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
        $this->domainsRepository = $this->entityManager->getRepository(Domains::class);
        $this->technologiesRepository = $this->entityManager->getRepository(Technologies::class);        
    }
    
    public function testDomainsListIsNotEmpty(): void {

        $this->assertNotEmpty($this->domainsRepository->findAll());
    }

    public function testCanNotAddDomainWithoutName(): void {
        
#        $this->expectException(NotNullConstraintViolationException::class);
        $this->assertFalse($this->domainsRepository->add(new Domains(), true));
    }
    
    public function testCanAddAndRemoveDummyDomain(): void {
        
#        $this->expectNotToPerformAssertions();

        $domain = new Domains();
        $domain->setName($this->dummy['name']);
        $this->assertTrue($this->domainsRepository->add($domain, true));
        $this->domainsRepository->remove($domain, true);
    }

    /**
     * @depends testDomainsListIsNotEmpty
     * @return void
     */
    public function testCanNotAddDuplicateDomain(): void {
        
#        $this->expectException(UniqueConstraintViolationException::class);
     
        $domain = $this->domainsRepository->findOneBy(array());
        $this->assertNotNull($domain);
      
        $new_domain = new Domains();
        $new_domain->setName($domain->getName());
#        $this->domainsRepository->add($new_domain, true);
        $this->assertFalse($this->domainsRepository->add($new_domain, true));
    }
    
    /**
     * @depends testDomainsListIsNotEmpty
     * @return void
     */
    public function testDomainRemovalLeadsToLinkedTechnologiesDeletion(): void {

        // Get random domain
        $domain = $this->domainsRepository->findOneBy(array());
        $this->assertNotNull($domain);
        
        // Add dummy technology for this domain
        $technology = new Technologies();
        $technology->setName($this->dummy['name']);
        $technology->setDomain($domain);
        $this->assertTrue($this->technologiesRepository->add($technology, true));
        
        $this->assertNotEmpty($technology = $this->technologiesRepository->findOneByDomain($domain));
        
        // Remove domain
        $this->domainsRepository->remove($domain, true);
        
        // Make sure no technologies for this domain present
        $this->assertEmpty($technology = $this->technologiesRepository->findOneByDomain($domain));
    }
}
