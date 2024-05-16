<?php

namespace App\Serializer\Normalizer;

use App\Entity\Tasks;
use App\Entity\TaskTechs;
use App\Entity\Technologies;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Doctrine\ORM\EntityManagerInterface;

class TaskTechsDenormalizer implements DenormalizerInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(
            EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): TaskTechs {
        $taskTech = new TaskTechs();
        if (is_array($data) && array_key_exists('task', $data) && 
                is_array($data['task']) && array_key_exists('path', $data['task'])) 
                {
            $task = $this->entityManager->getRepository(Tasks::class)->findOneByPath($data['task']['path']);
            if ($task) {
                $taskTech->setTask($task);
            }
        }
        if (is_array($data) && array_key_exists('tech', $data) && 
                is_array($data['tech']) && array_key_exists('name', $data['tech'])) 
                {
            $tech = $this->entityManager->getRepository(Technologies::class)->findOneByName($data['tech']['name']);
            if ($tech) {
                $taskTech->setTech($tech);
            }
        }
        return $taskTech;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool {
        return TaskTechs::class === $type;
    }
}
