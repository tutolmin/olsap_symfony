<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class BreedsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $csvContents = file_get_contents('/var/tmp/breeds.csv');

        $serializer = new Serializer(
                [new ObjectNormalizer(), new ArrayDenormalizer()],
                [new CsvEncoder()]);
        
        $breeds = $serializer->deserialize($csvContents, 'App\Entity\Breeds[]', 'csv');

        foreach ($breeds as $breed) {

            $manager->persist($breed);
        }
        
        $manager->flush();
    }
}
