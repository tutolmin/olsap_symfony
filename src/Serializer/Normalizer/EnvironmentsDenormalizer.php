<?php

namespace App\Serializer\Normalizer;

use App\Entity\EnvironmentStatuses;
use App\Entity\Tasks;
use App\Entity\Sessions;
use App\Entity\Instances;
use App\Entity\Environments;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Doctrine\ORM\EntityManagerInterface;

class EnvironmentsDenormalizer implements DenormalizerInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(
            EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Environments {
//        var_dump($data);
        $environment = new Environments();
        if (is_array($data) && array_key_exists('hash', $data)) {
            $environment->setHash($data['hash']);
        }
        if (is_array($data) && array_key_exists('startedAt', $data) && $data['startedAt']) {
            $environment->setStartedAt(new \DateTimeImmutable($data['startedAt']));
        }
        if (is_array($data) && array_key_exists('finishedAt', $data) && $data['finishedAt']) {
            $environment->setFinishedAt(new \DateTimeImmutable($data['finishedAt']));
        }
        if (is_array($data) && array_key_exists('status', $data)) 
                {
            $status = $this->entityManager->getRepository(EnvironmentStatuses::class)->
                    findOneByStatus($data['status']);
            if ($status) {
                $environment->setStatus($status);
            }
        }        
        if (is_array($data) && array_key_exists('session', $data)) 
                {
            $session = $this->entityManager->getRepository(Sessions::class)->
                    findOneByHash($data['session']);
            if ($session) {
                $environment->setSession($session);
            }
        }        
        if (is_array($data) && array_key_exists('instance', $data)) 
                {
            $instance = $this->entityManager->getRepository(Instances::class)->
                    findOneByName($data['instance']);
            if ($instance) {
                $environment->setInstance($instance);
            }
        }        
        if (is_array($data) && array_key_exists('task', $data)) 
                {
            $task = $this->entityManager->getRepository(Tasks::class)->
                    findOneByPath($data['task']);
            if ($task) {
                $environment->setTask($task);
            }
        }
        if (is_array($data) && array_key_exists('valid', $data) && $data['valid']) {
            $environment->setValid($data['valid']);
        }
        if (is_array($data) && array_key_exists('deployment', $data) && $data['deployment']) {
            $environment->setDeployment($data['deployment']);
        }
        if (is_array($data) && array_key_exists('verification', $data) && $data['verification']) {
            $environment->setVerification($data['verification']);
        }
        return $environment;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool {
        return Environments::class === $type;
    }
}
