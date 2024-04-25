<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AddressesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $csvContents = file_get_contents('/var/tmp/addresses.csv');

        $serializer = new Serializer(
                [new ObjectNormalizer(), new ArrayDenormalizer()],
                [new CsvEncoder()]);
        
        $addresses = $serializer->deserialize($csvContents, 'App\Entity\Addresses[]', 'csv');

        foreach ($addresses as $address) {

            $manager->persist($address);
        }
        
        $manager->flush();
    }
}
