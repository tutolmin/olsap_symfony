<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Serializer\Normalizer\TaskTechsDenormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class TaskTechsFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {

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
            TechnologiesFixtures::class,
        ];
    }

    public static function getGroups(): array {
        return ['tasks','technologies'];
    }

    public function load(ObjectManager $manager): void {
        $this->logger->debug(__METHOD__);

        $csvContents = file_get_contents('/var/tmp/task-techs.csv');

        $normalizers = [
            new TaskTechsDenormalizer($this->entityManager),
            new ArrayDenormalizer()
        ];

        $serializer = new Serializer($normalizers, [new CsvEncoder()]);

        $taskTechs = $serializer->deserialize($csvContents,
                'App\Entity\TaskTechs[]', 'csv');

        foreach ($taskTechs as $taskTech) {
            $manager->persist($taskTech);
        }

        $manager->flush();
    }
}
