<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Serializer\Normalizer\TaskInstanceTypesDenormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class TaskInstanceTypesFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {

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
            TasksFixtures::class,
            OperatingSystemsFixtures::class,
            HardwareProfilesFixtures::class,
        ];
    }

    public static function getGroups(): array {
        return ['tasks','technologies'];
    }

    public function load(ObjectManager $manager): void {
        $this->logger->debug(__METHOD__);

        $csvContents = file_get_contents('/var/tmp/task-instance-types.csv');

        $normalizers = [
            new TaskInstanceTypesDenormalizer($this->entityManager),
            new ArrayDenormalizer()
        ];

        $serializer = new Serializer($normalizers, [new CsvEncoder()]);

        $taskInstanceTypes = $serializer->deserialize($csvContents,
                'App\Entity\TaskInstanceTypes[]', 'csv');

        foreach ($taskInstanceTypes as $taskInstanceType) {
            $manager->persist($taskInstanceType);
        }

        $manager->flush();
    }
}
