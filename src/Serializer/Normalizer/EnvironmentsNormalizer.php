<?php

namespace App\Serializer\Normalizer;

use App\Entity\Environments;
use App\Entity\Sessions;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class EnvironmentsNormalizer implements NormalizerInterface {

    public function __construct(
            private ObjectNormalizer $normalizer,
    ) {
        
    }

    public function normalize($topic, ?string $format = null, array $context = []): array|\ArrayObject|bool|float|int|string|null {
        $data = $this->normalizer->normalize($topic, $format,
                
                //$patient->getDateAdded()->format('Y-m-d H:i:s');
                
                [AbstractNormalizer::ATTRIBUTES => 
                    ['hash', 
//                        'task' => ['path'], 'instance' => ['name'], 'status' => ['status'], 'startedAt', 'finishedAt',
                        'valid', 'deployment','verification']]);
//        var_dump($topic);
        if ($topic instanceof Environments) {

            if (is_array($data)) {

                $data['status'] = $topic->getStatus()->getStatus();
                $data['task'] = $topic->getTask()->getPath();

                if ($topic->getSession()) {
                    $data['session'] = $topic->getSession()->getHash();
                }

                if ($topic->getInstance()) {
                    $data['instance'] = $topic->getInstance()->getName();
                }

                if ($topic->getStartedAt()) {
                    $data['startedAt'] = $topic->getStartedAt()->format(\DateTimeImmutable::ISO8601);
                }
                
                if ($topic->getFinishedAt()) {
                    $data['finishedAt'] = $topic->getFinishedAt()->format(\DateTimeImmutable::ISO8601);
                }
            }
        }

        return $data;
    }

//    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    public function supportsNormalization($data, ?string $format = null): bool {
        return $data instanceof Environments;
    }

    /*
      public function getSupportedTypes(?string $format): array
      {
      return [
      'object' => null,             // Doesn't support any classes or interfaces
      '*' => false,                 // Supports any other types, but the result is not cacheable
      Environments::class => true, // Supports MyCustomClass and result is cacheable
      ];
      }
     */
}
