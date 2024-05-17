<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class EnvironmentStatusesFixtures extends Fixture implements FixtureGroupInterface {

    public static function getGroups(): array {
        return ['environments'];
    }

    public function load(ObjectManager $manager): void {
        $csvContents = file_get_contents('/var/tmp/environment-statuses.csv');

        $serializer = new Serializer(
                [new ObjectNormalizer(), new ArrayDenormalizer()],
                [new CsvEncoder()]);

        $sessionStatuses = $serializer->deserialize($csvContents,
                'App\Entity\EnvironmentStatuses[]', 'csv');

        foreach ($sessionStatuses as $sessionStatus) {

            $manager->persist($sessionStatus);
        }

        $manager->flush();
    }
}
