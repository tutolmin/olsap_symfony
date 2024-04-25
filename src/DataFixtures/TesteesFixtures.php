<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class TesteesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $csvContents = file_get_contents('/var/tmp/testees.csv');
/*
 * 
 * https://symfony.com/doc/6.4/components/serializer.html#recursive-denormalization-and-type-safety
 * 
 * 
 * 
 * 
$normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
$serializer = new Serializer([new DateTimeNormalizer(), $normalizer]);

$obj = $serializer->denormalize(
    ['inner' => ['foo' => 'foo', 'bar' => 'bar'], 'date' => '1988/01/21'],
    'Acme\ObjectOuter'
);

dump($obj->getInner()->foo); // 'foo'        
*/        
        $serializer = new Serializer(
                [new ObjectNormalizer(null, null, null, new ReflectionExtractor()), new ArrayDenormalizer(), new DateTimeNormalizer()],
                [new CsvEncoder()]);
        
        $testees = $serializer->deserialize($csvContents, 'App\Entity\Testees[]', 'csv');

        foreach ($testees as $testee) {
//echo $testee->getRegisteredAt()->format('Y-m-d H:i:s');
            $manager->persist($testee);
        }
        
        $manager->flush();
    }
}
