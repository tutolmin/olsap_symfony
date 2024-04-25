<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DomainsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $csvContents = file_get_contents('/var/tmp/domains.csv');

        $serializer = new Serializer(
                [new ObjectNormalizer(), new ArrayDenormalizer()],
                [new CsvEncoder()]);
        
        $domains = $serializer->deserialize($csvContents, 'App\Entity\Domains[]', 'csv');

        foreach ($domains as $domain) {

            $manager->persist($domain);
        }
        
        $manager->flush();
    }
}
