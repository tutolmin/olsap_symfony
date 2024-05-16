<?php

namespace App\Serializer\Normalizer;

use App\Entity\Tasks;
use App\Entity\OperatingSystems;
use App\Entity\HardwareProfiles;
use App\Entity\InstanceTypes;
use App\Entity\TaskInstanceTypes;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Doctrine\ORM\EntityManagerInterface;

class TaskInstanceTypesDenormalizer implements DenormalizerInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(
            EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): TaskInstanceTypes {
        $taskInstanceTypes = new TaskInstanceTypes();
        if (is_array($data) && array_key_exists('task', $data) && 
                is_array($data['task']) && array_key_exists('path', $data['task'])) 
                {
            $task = $this->entityManager->getRepository(Tasks::class)->findOneByPath($data['task']['path']);
            if ($task) {
                $taskInstanceTypes->setTask($task);
            }
        }        
        if (is_array($data) && array_key_exists('instanceType', $data) &&
                is_array($data['instanceType']) && array_key_exists('os', $data['instanceType']) && 
                array_key_exists('hwProfile', $data['instanceType'])) {

            $os = $this->entityManager->getRepository(OperatingSystems::class)->findOneByAlias(
                    $data['instanceType']['os']['alias']);
            $hp = $this->entityManager->getRepository(HardwareProfiles::class)->findOneByName(
                    $data['instanceType']['hwProfile']['name']);

//            var_dump($os);

            if ($os && $hp) {
                
                // Try to find existing Instance type
                $taskInstanceType = $this->entityManager->getRepository(InstanceTypes::class)->findOneBy(
                        ['os' => $os->getId(), 'hw_profile' => $hp->getId()]);

                if ($taskInstanceType) {
                    $taskInstanceTypes->setInstanceType($taskInstanceType);
                }
            }
        }

        return $taskInstanceTypes;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool {
        return TaskInstanceTypes::class === $type;
    }
}
