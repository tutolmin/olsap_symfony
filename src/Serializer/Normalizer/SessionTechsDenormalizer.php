<?php

namespace App\Serializer\Normalizer;

use App\Entity\Sessions;
use App\Entity\SessionTechs;
use App\Entity\Technologies;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Doctrine\ORM\EntityManagerInterface;

class SessionTechsDenormalizer implements DenormalizerInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(
            EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): SessionTechs {
        $sessionTech = new SessionTechs();
        if (is_array($data) && array_key_exists('session', $data) && 
                is_array($data['session']) && array_key_exists('hash', $data['session'])) 
                {
            $session = $this->entityManager->getRepository(Sessions::class)->findOneByHash($data['session']['hash']);
            if ($session) {
                $sessionTech->setSession($session);
            }
        }
        if (is_array($data) && array_key_exists('tech', $data) && 
                is_array($data['tech']) && array_key_exists('name', $data['tech'])) 
                {
            $tech = $this->entityManager->getRepository(Technologies::class)->findOneByName($data['tech']['name']);
            if ($tech) {
                $sessionTech->setTech($tech);
            }
        }
        return $sessionTech;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool {
        return SessionTechs::class === $type;
    }
}
