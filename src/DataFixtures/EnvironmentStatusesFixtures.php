<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\EnvironmentStatuses;

class EnvironmentStatusesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $env_statuses = array(
            "Complete" => null,
            "Verified" => null,
            "Solved" => null,
            "Skipped" => "User clicked Skipped button during test Session",
            "Deployed" => "Deployment playbook has been run for the Environment",
            "New" => "New Environment entity not linked to Session",
            "Created" => "Instance has been bound",
        );

        foreach ($env_statuses as $name => $desc) {

            $env_status = new EnvironmentStatuses();
            $env_status->setStatus($name);
            if ($desc) {
                $env_status->setDescription($desc);
            }
            $manager->persist($env_status);
        }

        $manager->flush();
    }
}
