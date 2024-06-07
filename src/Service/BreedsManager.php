<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Breeds;
use App\Repository\BreedsRepository;
use App\Service\OperatingSystemsManager;

class BreedsManager {

    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private BreedsRepository $breedsRepository;

    /**
     * 
     * @var OperatingSystemsManager
     */
    private $osManager;
    
    public function __construct(
            LoggerInterface $logger, EntityManagerInterface $em,
            OperatingSystemsManager $osManager
    ) {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
        $this->breedsRepository = $this->entityManager->getRepository(Breeds::class);
        
        $this->osManager = $osManager;
    }

    /**
     * 
     * @param Breeds $breed
     */
    public function removeBreed(Breeds $breed): void {

        $this->logger->debug(__METHOD__);

        foreach ($breed->getOperatingSystems() as $os) {
            $this->osManager->removeOperatingSystem($os);
        }
        
        $this->breedsRepository->remove($breed, true);
    }
}
