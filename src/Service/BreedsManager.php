<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\OperatingSystems;
use App\Entity\Breeds;
use App\Repository\OperatingSystemsRepository;
use App\Repository\BreedsRepository;

class BreedsManager {

    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private OperatingSystemsRepository $osRepository;
    private BreedsRepository $breedsRepository;

    public function __construct(
            LoggerInterface $logger, EntityManagerInterface $em
    ) {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
        $this->osRepository = $this->entityManager->getRepository(OperatingSystems::class);
        $this->breedsRepository = $this->entityManager->getRepository(Breeds::class);
    }

    /**
     * 
     * @param Breeds $breed
     * @param bool $cascade
     * @return bool
     */
    public function removeBreed(Breeds $breed, 
            bool $cascade = false): bool {

        $this->logger->debug(__METHOD__);

        $oses = $this->osRepository->findBy(['breed' => $breed->getId()]);

        if ($oses && !$cascade) {
            $this->logger->debug("Can't delete corresponding Operating Systems without cascade flag.");
            return false;
        }
        
        if ($this->breedsRepository->remove($breed, true)) {
            return true;
        }
        return false;
    }
}
