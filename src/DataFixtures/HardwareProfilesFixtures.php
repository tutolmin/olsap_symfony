<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\HardwareProfiles;

class HardwareProfilesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $hw_profiles = array(
            [true, "VM with 1 CPU and 1G memory", 1500, "baseball", false],
            [false, "Container, 10% CPU allowance, 256MB memory, no swap, 1GB root", 10, "cricket", true],
            [true, "VM with 1 CPU and 512M memory", 1000, "tennis", false],
            [false, "Container, 10% CPU allowance, 256MB memory, 128MB swap, 1GB root", 20, "soccer", true],
        );

        foreach ($hw_profiles as $profile) {

            $hw_profile = new HardwareProfiles();
            $hw_profile->setType($profile[0]);
            $hw_profile->setDescription($profile[1]);
            $hw_profile->setCost($profile[2]);            
            $hw_profile->setName($profile[3]);
            $hw_profile->setSupported($profile[4]);
            $manager->persist($hw_profile);
        }

        $manager->flush();
    }
}
