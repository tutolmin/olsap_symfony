<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Service\LxcManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class InstancesFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {

    private LxcManager $lxcService;

    public function __construct(LxcManager $lxcManager) {

        $this->lxcService = $lxcManager;
    }
    
    /**
     * 
     * @return array<int, string>
     */
    public function getDependencies()
    {
        return [
            OperatingSystemsFixtures::class,
            HardwareProfilesFixtures::class,
            InstanceStatusesFixtures::class,
            AddressesFixtures::class,
        ];
    }

    public static function getGroups(): array {
        return ['instances'];
    }

    public function load(ObjectManager $manager): void {

        // Use Lxc service method
        $objects = $this->lxcService->getObjectList();

        if (!$objects) {
            return;
        }
//            var_dump($objects);

        foreach ($objects as $object) {
            $info = $this->lxcService->getObjectInfo($object);
            if ($info) {
                $this->lxcService->importInstance( $info);
            }
        }
    }
}
