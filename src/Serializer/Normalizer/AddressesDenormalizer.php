<?php

namespace App\Serializer\Normalizer;

use App\Entity\Addresses;
use App\Entity\Ports;
//use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
#use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
#use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
#use App\Repository\PortsRepository;
use Doctrine\ORM\EntityManagerInterface;
#use Doctrine\Persistence\ObjectManager;

class AddressesDenormalizer implements DenormalizerInterface
{
    private EntityManagerInterface $entityManager;
#    private ObjectNormalizer $objectNormalizer;

#    private ObjectManager $objectManager;

    public function __construct(
#            ObjectNormalizer $objectNormalizer, 

#	ObjectManager $objectManager,
EntityManagerInterface $entityManager
)
    {
#        $this->objectNormalizer = $objectNormalizer;
        $this->entityManager = $entityManager;
#       $this->objectManager = $objectManager;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Addresses
    {
        $address = new Addresses();
        if (is_array($data) && array_key_exists('ip', $data)) {
            $address->setIp($data['ip']);
        }
        if (is_array($data) && array_key_exists('mac', $data)) {

            $address->setMac($data['mac']);
        }

        if (is_array($data) && array_key_exists('port', $data)) {
//$port = $this->entityManager->getRepository(Ports::class)->find($data['port']);
$port = $this->entityManager->getRepository(Ports::class)->findOneByNumber($data['port']);
//$port = $this->objectManager->getRepository(Ports::class)->findByPort($data['port']);
            $address->setPort($port);
}
        return $address;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return Addresses::class === $type;
    }
}
