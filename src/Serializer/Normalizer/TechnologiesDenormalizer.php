<?php

namespace App\Serializer\Normalizer;

use App\Entity\Domains;
use App\Entity\Technologies;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Doctrine\ORM\EntityManagerInterface;

class TechnologiesDenormalizer implements DenormalizerInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(
            EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Technologies {
        $technology = new Technologies();
        if (is_array($data) && array_key_exists('name', $data)) {
            $technology->setName($data['name']);
        }
        if (is_array($data) && array_key_exists('description', $data) && $data['description']) {
            $technology->setDescription($data['description']);
        }
        if (is_array($data) && array_key_exists('domain', $data) && 
                is_array($data['domain']) && array_key_exists('name', $data['domain'])) 
                {
            $domain = $this->entityManager->getRepository(Domains::class)->findOneByName($data['domain']['name']);
            if ($domain) {
                $technology->setDomain($domain);
            }
        }
        return $technology;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool {
        return Technologies::class === $type;
    }
}
