<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Domains;
use App\Entity\Technologies;
//use Doctrine\ORM\EntityManagerInterface;
//use App\Repository\DomainsRepository;
use Psr\Log\LoggerInterface;

class TechnologiesFixtures extends Fixture
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
//        parent::__construct();
        
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);
    }

    public function load(ObjectManager $manager): void {
        $this->logger->debug(__METHOD__);
        
        $techs = array(
            "LVM" => ["Storage", "Logical Volume Manager configuration, managing and troubleshooting."],
            "Ceph" => ["Storage", "Configuring and troubleshooting Ceph storage."],
            "Netplan" => ["Network", "Configuring the network with netplan."],
            "NetworkManager" => ["Network", "Configuring the network with NetworkManager."],
            "Bonding" => ["Network", "Configure interface bonding."],
            "ACL" => ["Security", "Setting and removing file ACLs"],
            "Permissions" => ["Security", "Changing file permissions on filesystems."],
            "Ansible" => ["Automation", "Configuration management."],
            "Tuned" => ["Performance", "Special profiles for system parameters"],
            "Filesystems" => ["Storage", "creating, resizing, repairing"],
            "RPM" => ["Software", null],
            "DNF" => ["Software", null],
            "docker" => ["Virtualization", null],
            "APT" => ["Software", null],
            "Files and directories" => ["System management", null],
            "NFS" => ["Storage", null],
            "Systemd" => ["System management", null],
            "Firewall" => ["Network", null],
            "Remote access" => ["System management", "Access via SSH"],
            "Network traffic" => ["Monitoring", "capture, analyze network traffic."],
            "View processes" => ["Monitoring", "top, nmon, etc."],
            "I/O activity" => ["Monitoring", "view I/O activity, iostat"],
            "Dpkg" => ["Software", "use debian package manager"],
            "dmidecode" => ["Hardware", "dmidecode  is a tool for dumping a computer's DMI (some say SMBIOS) table contents in a human-readable format."],
            "Network Settings" => ["Network", "Network subsystem settings"],
            "Archiving" => ["System management", "ZIP, GZip, TAR, etc."],
            "Users and groups" => ["System management", "Adding, removing, managing users and groups"],
        );
        foreach ($techs as $name => $tech_arr) {

//            $this->logger->debug("Tech: " . $name);

            $tech = new Technologies();
            $tech->setName($name);

            if ($tech_arr[1]) {
                $tech->setDescription($tech_arr[1]);
            }
//            $this->logger->debug("Domain: " . $tech_arr[0]);

            $domainsRepository = $manager->getRepository(Domains::class);
            $domain = $domainsRepository->findOneByName($tech_arr[0]);

            $this->logger->debug("Domain: " . $domain);

            if ($domain) {
                $tech->setDomain($domain);
            }

            $manager->persist($tech);
        }

        $manager->flush();
    }
}
