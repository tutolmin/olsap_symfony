<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\InstanceStatuses;

class InstanceStatusesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void {
        $instance_statuses = array(
            "Bound" => null,
            "Sleeping" => "Stopped LXC instance bound to an active environment",
            "Started" => "Unbound started LXC instance, ready for allocation",
            "New" => "New Instance entity not linked to an actual LXC instance",
            "Running" => "Started LXC instance bound to an active Environment",
            "Stopped" => "Unbound stopped LXC instance, ready for allocation",
        );

        foreach ($instance_statuses as $name => $desc) {

            $instance_status = new InstanceStatuses();
            $instance_status->setStatus($name);
            if ($desc) {
                $instance_status->setDescription($desc);
            }
            $manager->persist($instance_status);
        }

        $manager->flush();
    }
}
