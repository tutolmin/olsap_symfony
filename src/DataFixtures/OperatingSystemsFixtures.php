<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\OperatingSystems;
use App\Entity\Breeds;
use Psr\Log\LoggerInterface;
use App\Service\OperatingSystemsManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OperatingSystemsFixtures extends Fixture implements DependentFixtureInterface
{
    private LoggerInterface $logger;
    private OperatingSystemsManager $osManager;
    
    public function __construct(LoggerInterface $logger,
            OperatingSystemsManager $osManager)
    {
//        parent::__construct();
        
        $this->logger = $logger;
        $this->osManager = $osManager;
        $this->logger->debug(__METHOD__);
    }
    
    /**
     * 
     * @return array<int, string>
     */
    public function getDependencies()
    {
        return [
            BreedsFixtures::class,
        ];
    }
    
    public function load(ObjectManager $manager): void
    {
        $operating_systems = array(
            ["18.04 LTS", "Major version", true, "Ubuntu", "bionic"],
            ["33", "Older version", false, "Fedora", "f33"],
            ["22.04 LTS", "Modern version", false, "Ubuntu", "jammy"],
            ["20.04 LTS", "Current version", true, "Ubuntu", "focal"],
            ["38", "New version", true, "Fedora", "f38"],
            ["39", "Latest version", false, "Fedora", "f39"],
        );

        foreach ($operating_systems as $operating_system) {

            $os = new OperatingSystems();
            $os->setRelease($operating_system[0]);
            $os->setDescription($operating_system[1]);
            $os->setSupported($operating_system[2]);
            $os->setAlias($operating_system[4]);
            
            $breedsRepository = $manager->getRepository(Breeds::class);
            $breed = $breedsRepository->findOneByName($operating_system[3]);

            $this->logger->debug("Breed: " . $breed);
            
            if ($breed) {
                $os->setBreed($breed);
                $this->osManager->addOperatingSystem($os);
            }

            /*
            $manager->persist($os);

            // Add corresponding instance types
            if ($os->isSupported()) {
                $this->osManager->addInstanceTypes($os);
            }
 * 
 */
        }

        $manager->flush();
    }
}
