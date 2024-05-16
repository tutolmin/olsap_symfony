<?php

namespace App\Serializer\Normalizer;

use App\Entity\Tasks;
use App\Entity\TaskOses;
use App\Entity\OperatingSystems;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Doctrine\ORM\EntityManagerInterface;

class TaskOsesDenormalizer implements DenormalizerInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(
            EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): TaskOses {
        $taskOs = new TaskOses();
        if (is_array($data) && array_key_exists('task', $data) && 
                is_array($data['task']) && array_key_exists('path', $data['task'])) 
                {
            $task = $this->entityManager->getRepository(Tasks::class)->findOneByPath($data['task']['path']);
            if ($task) {
                $taskOs->setTask($task);
            }
        }
        if (is_array($data) && array_key_exists('os', $data) && 
                is_array($data['os']) && array_key_exists('alias', $data['os'])) 
                {
            $os = $this->entityManager->getRepository(OperatingSystems::class)->findOneByAlias($data['os']['alias']);
            if ($os) {
                $taskOs->setOs($os);
            }
        }
        return $taskOs;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool {
        return TaskOses::class === $type;
    }
}
