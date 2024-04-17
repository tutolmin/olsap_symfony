<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\SessionStatuses;

class SessionStatusesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statuses = array(
            "New",
            "Ready",
            "Allocated",
            "Started",
            "Finished",
            "TimedOut",
        );
        
        foreach ($statuses as $name) {
            
        $breed = new SessionStatuses();
        $breed->setStatus($name);
        $manager->persist($breed); 
        }

        $manager->flush();
    }
}
