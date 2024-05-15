<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use App\Service\OperatingSystemsManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Serializer\Normalizer\OperatingSystemsDenormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class OperatingSystemsFixtures extends Fixture implements DependentFixtureInterface {

    private LoggerInterface $logger;
    private OperatingSystemsManager $osManager;
    private EntityManagerInterface $entityManager;

    public function __construct(LoggerInterface $logger,
            OperatingSystemsManager $osManager,
            EntityManagerInterface $entityManager
    ) {
//        parent::__construct();

        $this->logger = $logger;
        $this->osManager = $osManager;
        $this->entityManager = $entityManager;
        $this->logger->debug(__METHOD__);
    }

    /**
     * 
     * @return array<int, string>
     */
    public function getDependencies() {
        return [
            BreedsFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void {
        
        $csvContents = file_get_contents('/var/tmp/operating-systems.csv');

        $normalizers = [
            new OperatingSystemsDenormalizer($this->entityManager),
            new ArrayDenormalizer()
        ];

        $serializer = new Serializer($normalizers, [new CsvEncoder()]);

        $operating_systems = $serializer->deserialize($csvContents, 
                 'App\Entity\OperatingSystems[]', 'csv');

        foreach ($operating_systems as $operating_system) {
            $this->osManager->addOperatingSystem($operating_system);
        }

        $manager->flush();
    }
}
