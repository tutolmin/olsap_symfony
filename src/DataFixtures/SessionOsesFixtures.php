<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Serializer\Normalizer\SessionOsesDenormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class SessionOsesFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {

    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;

    public function __construct(LoggerInterface $logger,
            EntityManagerInterface $entityManager
    ) {
//        parent::__construct();

        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->logger->debug(__METHOD__);
    }

    /**
     * 
     * @return array<int, string>
     */
    public function getDependencies() {
        return [
            SessionsFixtures::class,
            OperatingSystemsFixtures::class,
        ];
    }

    public static function getGroups(): array {
        return ['sessions','oses'];
    }

    public function load(ObjectManager $manager): void {
        $this->logger->debug(__METHOD__);

        $csvContents = file_get_contents('/var/tmp/session-oses.csv');

        $normalizers = [
            new SessionOsesDenormalizer($this->entityManager),
            new ArrayDenormalizer()
        ];

        $serializer = new Serializer($normalizers, [new CsvEncoder()]);

        $sessionOses = $serializer->deserialize($csvContents,
                'App\Entity\SessionOses[]', 'csv');

        foreach ($sessionOses as $sessionOs) {
            $manager->persist($sessionOs);
        }

        $manager->flush();
    }
}
