<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Domains;
use App\Entity\Technologies;
use App\Repository\DomainsRepository;
use App\Repository\TechnologiesRepository;

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

//        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
	$this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->domainsRepository = $this->entityManager->getRepository(Domains::class);
        $this->technologiesRepository = $this->entityManager->getRepository(Technologies::class);        
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
     * @return Domains|null
     */
    public function testDomainHasTechnologiesReference(array $domains): ?Domains {
        
        foreach ($domains as $domain) {
            if($domain->getTechnologies()->first()){
                $this->assertTrue(true);
                return $domain;
            }
        }
        
        $this->assertTrue(false);
        return null;
    }
    
    public function testCanNotAddDomainWithoutName(): void {
        
        $this->assertFalse($this->domainsRepository->add(new Domains(), true));
    }
    
    /**
     * @depends testDomainsListIsNotEmpty
     * @return void
     */
    public function testCanNotAddDuplicateDomain(): void {
        
        $domain = $this->domainsRepository->findOneBy(array());
        $this->assertNotNull($domain);
      
        $new_domain = new Domains();
        $new_domain->setName($domain->getName());
        $this->assertFalse($this->domainsRepository->add($new_domain, true));
    }
    
    /**
     * 
     * @return Domains
     */
    public function testCanAddDummyDomain(): Domains {

        $domain = new Domains();
        $domain->setName($this->dummy['name']);
        $this->assertTrue($this->domainsRepository->add($domain, true));
        return $domain;
    }

    /**
     * @depends testCanAddDummyDomain
     * @param Domains $domain
     * @return void
     */
    public function testCanRemoveDummyDomain(Domains $domain): void {
        
        $this->assertTrue($this->domainsRepository->remove($domain));
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
        
        $this->assertNotEmpty($this->technologiesRepository->findOneByDomain($domain));
        
        // Remove domain
        $this->domainsRepository->remove($domain, true);
        
        // Make sure no technologies for this domain present
        $this->assertEmpty($this->technologiesRepository->findOneByDomain($domain));
    }
}
