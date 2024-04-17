<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Domains;

class DomainsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $domains = array(
            "Software" => "yum, dnf, rpm",
            "Virtualization" => "docker, LXC",
            "Security" => "SELinux, passwords, kerberos, openssl, file permissions, ACLs, etc.",
            "Hardware" => "lspci, dmidecode",
            "Automation" => "Collection of tasks related to automating repeating work.",
            "Monitoring" => "Get system running status and metrics.",
            "Network" => "Tasks related to network configuration, monitoring and troubleshooting.",
            "Performance" => "Tuning system parameters.",
            "Storage" => "Everything related to storage subsystem, including volume managers, working with block devices, mounting, formatting, etc.",
            "System management" => "systemd, processes",
        );
        
        foreach ($domains as $name => $desc) {
            
        $domain = new Domains();
        $domain->setName($name);
        $domain->setDescription($desc);
        $manager->persist($domain); 
        }

        $manager->flush();
    }
}
