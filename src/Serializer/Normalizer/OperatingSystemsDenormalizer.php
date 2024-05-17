<?php

namespace App\Serializer\Normalizer;

use App\Entity\Breeds;
use App\Entity\OperatingSystems;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Doctrine\ORM\EntityManagerInterface;

class OperatingSystemsDenormalizer implements DenormalizerInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(
            EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): OperatingSystems {
        $os = new OperatingSystems();
        if (is_array($data) && array_key_exists('release', $data)) {
            $os->setRelease($data['release']);
        }
        if (is_array($data) && array_key_exists('description', $data) && $data['description']) {
            $os->setDescription($data['description']);
        }
        if (is_array($data) && array_key_exists('supported', $data)) {
            $os->setSupported($data['supported']);
        }
        if (is_array($data) && array_key_exists('alias', $data)) {
            $os->setAlias($data['alias']);
        }
        if (is_array($data) && array_key_exists('breed', $data) 
                && is_array($data['breed']) && array_key_exists('name', $data['breed'])) 
                {
            $breed = $this->entityManager->getRepository(Breeds::class)->findOneByName($data['breed']['name']);
            if ($breed) {
                $os->setBreed($breed);
            }
        }
        return $os;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool {
        return OperatingSystems::class === $type;
    }
}
