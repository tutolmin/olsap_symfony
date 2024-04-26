<?php

namespace App\Serializer\Normalizer;

use App\Entity\Addresses;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class AddressesNormalizer implements NormalizerInterface
{
    public function __construct(
        private ObjectNormalizer $normalizer,
    ) {
    }

//    public function normalize($topic, ?string $format = null, array $context = []): array
    public function normalize($topic, ?string $format = null, array $context = []): array|\ArrayObject|bool|float|int|string|null
    {        
        $data = $this->normalizer->normalize($topic, $format, 
                [AbstractNormalizer::ATTRIBUTES => ['ip','mac']]);
        if($topic instanceof Addresses) {
        
            $data['port'] = $topic->getPort();
        }
        
        return $data;
    }

//    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    public function supportsNormalization($data, ?string $format = null): bool
    {
        return $data instanceof Addresses;
    }
     
    public function getSupportedTypes(?string $format): array
    {
        return [
            'object' => null,             // Doesn't support any classes or interfaces
            '*' => false,                 // Supports any other types, but the result is not cacheable
            Addresses::class => true, // Supports MyCustomClass and result is cacheable
        ];
    }
}