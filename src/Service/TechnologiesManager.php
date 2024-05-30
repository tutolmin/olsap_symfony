<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Technologies;
use App\Repository\TechnologiesRepository;

class TechnologiesManager
{
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private TechnologiesRepository $technologyRepository;

    public function __construct( LoggerInterface $logger, EntityManagerInterface $em, 
)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        $this->entityManager = $em;
        $this->technologyRepository = $this->entityManager->getRepository( Technologies::class);
    }

    /**
     * 
     * @param Technologies $technology
     * @return bool
     */
    public function removeTechnology(Technologies $technology): bool {

        $this->technologyRepository->remove($technology, true);

        return true;
    }
}

