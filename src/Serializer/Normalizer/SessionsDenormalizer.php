<?php

namespace App\Serializer\Normalizer;

use App\Entity\SessionStatuses;
use App\Entity\Testees;
use App\Entity\Sessions;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Doctrine\ORM\EntityManagerInterface;

class SessionsDenormalizer implements DenormalizerInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(
            EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Sessions {
        $sessions = new Sessions();
        if (is_array($data) && array_key_exists('hash', $data)) {
            $sessions->setHash($data['hash']);
        }
        if (is_array($data) && array_key_exists('createdAt', $data)) {
            $sessions->setCreatedAt(new \DateTimeImmutable($data['createdAt']));
        }
        if (is_array($data) && array_key_exists('startedAt', $data) && $data['startedAt']) {
            $sessions->setStartedAt(new \DateTimeImmutable($data['startedAt']));
        }
        if (is_array($data) && array_key_exists('finishedAt', $data) && $data['finishedAt']) {
            $sessions->setFinishedAt(new \DateTimeImmutable($data['finishedAt']));
        }
        if (is_array($data) && array_key_exists('status', $data) && 
                is_array($data['status']) && array_key_exists('status', $data['status'])) 
                {
            $status = $this->entityManager->getRepository(SessionStatuses::class)->
                    findOneByStatus($data['status']['status']);
            if ($status) {
                $sessions->setStatus($status);
            }
        }        
        if (is_array($data) && array_key_exists('testee', $data) && 
                is_array($data['testee']) && array_key_exists('oauthToken', $data['testee'])) 
                {
            $testee = $this->entityManager->getRepository(Testees::class)->
                    findOneByOauthToken($data['testee']['oauthToken']);
            if ($testee) {
                $sessions->setTestee($testee);
            }
        }
        return $sessions;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool {
        return Sessions::class === $type;
    }
}
