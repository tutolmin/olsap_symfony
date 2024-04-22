<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Breeds;
use App\Repository\BreedsRepository;

class BreedsTest extends KernelTestCase
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
     * @var BreedsRepository
     */
    private $breedsRepository;

    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get('Doctrine\ORM\EntityManagerInterface');
        $this->breedsRepository = $this->entityManager->getRepository(Breeds::class);
    }
    
    public function testBreedsListIsNotEmpty(): void {

        $this->assertNotEmpty($this->breedsRepository->findAll());
    }

    public function testCanNotAddBreedWithoutName(): void {
        
        $this->assertFalse($this->breedsRepository->add(new Breeds(), true));
    }

    public function testCanAddAndRemoveDummyBreed(): void {
        
        $breed = new Breeds();
        $breed->setName($this->dummy['name']);
        $this->assertTrue($this->breedsRepository->add($breed, true));
        $this->breedsRepository->remove($breed, true);
    }

    /**
     * @depends testBreedsListIsNotEmpty
     * @return void
     */
    public function testCanNotAddDuplicateBreed(): void {
        
        $breed = $this->breedsRepository->findOneBy(array());
        $this->assertNotNull($breed);
      
        $new_breed = new Breeds();
        $new_breed->setName($breed->getName());
        $this->assertFalse($this->breedsRepository->add($new_breed, true));
    }
}
