<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Serializer\Normalizer\AddressesDenormalizer;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
//use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
//use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
//use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
//use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class AddressesFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * 
     * @return array<int, string>
     */
    public function getDependencies()
    {
        return [
            PortsFixtures::class,
        ];
    }
    
    public function load(ObjectManager $manager): void
    {
        $csvContents = file_get_contents('/var/tmp/addresses.csv');
/*
        // all callback parameters are optional (you can omit the ones you don't use)
        $portCallback = function (object $innerObject, object $outerObject, string $attributeName, ?string $format = null, array $context = []): string {
//            return $innerObject instanceof \DateTimeImmutable ? $innerObject->format(\DateTimeImmutable::ISO8601) : '';

            };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'port' => $portCallback,
            ],
        ];

        $normalizer = new GetSetMethodNormalizer(null, null, null, null, null, $defaultContext);
        $serializer = new Serializer([$normalizer, new ArrayDenormalizer()], [new CsvEncoder()]);
*/        

        $normalizers = [
            new AddressesDenormalizer(),
//            new ObjectNormalizer(),
            new ArrayDenormalizer()
        ];

        $serializer = new Serializer($normalizers, [new CsvEncoder()]);
/*
        $result = $serializer->denormalize(
           $historique,
           Historique::class,
           'json',
           [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s']
        );
*/
        /*        
        $serializer = new Serializer(
                [new ObjectNormalizer(), new ArrayDenormalizer()],
                [new CsvEncoder()]);
 * 
 */        
        $addresses = $serializer->deserialize($csvContents, 'App\Entity\Addresses[]', 'csv');

        foreach ($addresses as $address) {

            $manager->persist($address);
        }
        
        $manager->flush();
    }
}
