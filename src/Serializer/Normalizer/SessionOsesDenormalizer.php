<?php

namespace App\Serializer\Normalizer;

use App\Entity\Sessions;
use App\Entity\SessionOses;
use App\Entity\OperatingSystems;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Doctrine\ORM\EntityManagerInterface;

class SessionOsesDenormalizer implements DenormalizerInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(
            EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): SessionOses {
        $sessionOs = new SessionOses();
        if (is_array($data) && array_key_exists('session', $data) && 
                is_array($data['session']) && array_key_exists('hash', $data['session'])) 
                {
            $session = $this->entityManager->getRepository(Sessions::class)->findOneByHash($data['session']['hash']);
            if ($session) {
                $sessionOs->setSession($session);
            }
        }
        if (is_array($data) && array_key_exists('os', $data) && 
                is_array($data['os']) && array_key_exists('alias', $data['os'])) 
                {
            $os = $this->entityManager->getRepository(OperatingSystems::class)->findOneByAlias($data['os']['alias']);
            if ($os) {
                $sessionOs->setOs($os);
            }
        }
        return $sessionOs;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool {
        return SessionOses::class === $type;
    }
}
