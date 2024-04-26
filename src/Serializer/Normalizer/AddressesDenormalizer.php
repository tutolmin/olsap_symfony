<?php

namespace App\Serializer\Normalizer;

use App\Entity\Addresses;
//use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class AddressesDenormalizer implements DenormalizerInterface
{

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Addresses
    {
        $address = new Addresses();
        if (is_array($data) && array_key_exists('ip', $data)) {
            $address->setIp($data['ip']);
        }
        if (is_array($data) && array_key_exists('mac', $data)) {

            $address->setMac($data['mac']);
        }
        return $address;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return Addresses::class === $type;
    }
}