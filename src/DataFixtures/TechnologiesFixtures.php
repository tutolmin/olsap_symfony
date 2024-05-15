<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Serializer\Normalizer\TechnologiesDenormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class TechnologiesFixtures extends Fixture implements DependentFixtureInterface {

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
            DomainsFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void {
        $this->logger->debug(__METHOD__);

        $csvContents = file_get_contents('/var/tmp/technologies.csv');

        $normalizers = [
            new TechnologiesDenormalizer($this->entityManager),
            new ArrayDenormalizer()
        ];

        $serializer = new Serializer($normalizers, [new CsvEncoder()]);

        $technologies = $serializer->deserialize($csvContents,
                'App\Entity\Technologies[]', 'csv');

        foreach ($technologies as $technology) {
            $manager->persist($technology);
        }

        $manager->flush();
    }
}
