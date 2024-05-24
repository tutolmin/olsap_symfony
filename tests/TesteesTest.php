<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Testees;
use App\Repository\TesteesRepository;

class TesteesTest extends KernelTestCase
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
    
    protected function setUp(): void {
        self::bootKernel();

//        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
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

    public function testCanNotAddTesteeWithoutMandatoryFields(): void {
        
        $this->assertFalse($this->testeesRepository->add(new Testees(), true));
    }
    
    public function testCanAddDummyTestee(): void {

        $new_testee = new Testees();
        $new_testee->setEmail($this->dummy['email']);
        $new_testee->setOauthToken($this->dummy['oauth_token']);
        $new_testee->setRegisteredAt(new \DateTimeImmutable('now'));

        $this->assertTrue($this->testeesRepository->add($new_testee, true));
    }

    /**
     * @depends testTesteesListIsNotEmpty
     * @param array<Testees> $testees
     * @return void
     */
    public function testCanRemoveRandomTestee( array $testees): void {

        $testee = $this->testeesRepository->findOneById($testees[0]->getId());
        $this->assertNotNull($testee);
        $id = $testee->getId();
    
        $this->testeesRepository->remove($testee, true);
        
        $removed_testee = $this->testeesRepository->findOneById($id);
        $this->assertNull($removed_testee);
    }
    
    /**
     * @depends testTesteesListIsNotEmpty
     * @return void
     */
    public function testCanNotAddDuplicateTestee(): void {
                
        $testees = $this->testeesRepository->findOneBy(array());
        $this->assertNotNull($testees);
      
        $new_testee = new Testees();
        $new_testee->setEmail($testees->getEmail());
        $new_testee->setOauthToken($testees->getOauthToken());
        $new_testee->setRegisteredAt($testees->getRegisteredAt());
        
        $this->assertFalse($this->testeesRepository->add($new_testee, true));
    }
}
