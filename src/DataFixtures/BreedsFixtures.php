<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Breeds;

class BreedsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $breeds = array(
            "Ubuntu",
            "Fedora",
        );
        
        foreach ($breeds as $name) {
            
        $breed = new Breeds();
        $breed->setName($name);
        $manager->persist($breed); 
        }

        $manager->flush();
    }
}
