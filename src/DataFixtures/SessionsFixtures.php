<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Serializer\Normalizer\SessionsDenormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class SessionsFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {

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
            TesteesFixtures::class, 
            SessionStatusesFixtures::class
        ];
    }

    public static function getGroups(): array {
        return ['sessions'];
    }

    public function load(ObjectManager $manager): void {
        $this->logger->debug(__METHOD__);

        $csvContents = file_get_contents('/var/tmp/sessions.csv');

        $normalizers = [
            new SessionsDenormalizer($this->entityManager),
            new ArrayDenormalizer()
        ];

        $serializer = new Serializer($normalizers, [new CsvEncoder()]);

        $sessions = $serializer->deserialize($csvContents,
                'App\Entity\Sessions[]', 'csv');

        foreach ($sessions as $session) {
            $manager->persist($session);
        }

        $manager->flush();
    }
}
