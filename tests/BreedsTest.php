<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Breeds;
use App\Entity\OperatingSystems;
use App\Repository\BreedsRepository;
use App\Repository\OperatingSystemsRepository;
use App\Service\BreedsManager;

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

    /**
     * 
     * @var OperatingSystemsRepository
     */
    private $osRepository;
    
    /**
     * 
     * @var BreedsManager
     */
    private $breedsManager;
    
    protected function setUp(): void {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->breedsRepository = $this->entityManager->getRepository(Breeds::class);
        $this->osRepository = $this->entityManager->getRepository(OperatingSystems::class);
        $this->breedsManager = static::getContainer()->get(BreedsManager::class);        
    }
    
    /**
     * 
     * @return array<Breeds>
     */
    public function testBreedsListIsNotEmpty(): array {

        $breeds = $this->breedsRepository->findAll();
        $this->assertNotEmpty($breeds);
        
        return $breeds;
    }
    
    /**
     * 
     * @depends testBreedsListIsNotEmpty
     * @param array<Breeds> $breeds
     * @return Breeds|null
     */
    public function testBreedHasOperatingSystemsReference(array $breeds): ?Breeds {
        
        foreach ($breeds as $breed) {
            if($breed->getOperatingSystems()->first()){
                $this->assertTrue(true);
                return $breed;
            }
        }
        
        $this->assertTrue(false);
        return null;
    }
    
    public function testCanNotAddBreedWithoutName(): void {
        
        $this->assertFalse($this->breedsRepository->add(new Breeds(), true));
    }

    /**
     * 
     * @depends testBreedsListIsNotEmpty
     * @param array<Breeds> $breeds
     * @return void
     */
    public function testCanNotAddDuplicateBreed(array $breeds): void {

        $new_breed = new Breeds();
        $new_breed->setName($breeds[0]->getName());
        $this->assertFalse($this->breedsRepository->add($new_breed, true));
    }

    /**
     * 
     * @return Breeds
     */
    public function testCanAddDummyBreed(): Breeds {
        
        $breed = new Breeds();
        $breed->setName($this->dummy['name']);
        $this->assertTrue($this->breedsRepository->add($breed, true));
        return $breed;
    }
        
            
    /**
     * @depends testBreedsListIsNotEmpty
     * @param array<Breeds> $breeds
     * @return void
     */    
    public function testCanRemoveAllBreeds($breeds): void {

        foreach ($breeds as $s) {

            $item = $this->breedsRepository->findOneById($s);
            $this->assertNotNull($item);
            $id = $item->getId();

            $this->breedsManager->removeBreed($item);

            $removed_item = $this->breedsRepository->findOneById($id);
            $this->assertNull($removed_item);
        }

        $this->assertEmpty($this->osRepository->findAll());
    }    
}
