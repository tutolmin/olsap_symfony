<?php

namespace App\Repository;

use Psr\Log\LoggerInterface;

use App\Entity\Instances;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\InstanceStatuses;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @extends ServiceEntityRepository<Instances>
 *
 * @method Instances|null find($id, $lockMode = null, $lockVersion = null)
 * @method Instances|null findOneBy(array $criteria, array $orderBy = null)
 * @method Instances[]    findAll()
 * @method Instances[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstancesRepository extends ServiceEntityRepository
{
    private $logger;
    private $instanceStatusesRepository;
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        parent::__construct($registry, Instances::class);

        $this->entityManager = $em;

        $this->instanceStatusesRepository = $this->entityManager->getRepository( InstanceStatuses::class);
    }

    public function add(Instances $entity, bool $flush = false): void
    {
        $this->logger->debug(__METHOD__);

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Instances $entity, bool $flush = false): void {
        $this->logger->debug(__METHOD__);

        // Fetch all linked Addresses and release them
        $addresses = $entity->getAddresses();
        foreach ($addresses as $address) {
            $address->setInstance(null);
        }
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByTypeAndStatus($instance_type, $status_string): ?Instances
    {
        $this->logger->debug(__METHOD__);

        $status = $this->instanceStatusesRepository->findOneByStatus($status_string);

	// TODO: check for valid result

        return $this->createQueryBuilder('i')
            ->where('i.status = :status')
            ->andWhere('i.instance_type = :instance_type')
            ->setParameter('status', $status->getId())
            ->setParameter('instance_type', $instance_type->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAllSpare($instance_type): array {
        $this->logger->debug(__METHOD__);

        $status_started = $this->instanceStatusesRepository->findOneByStatus("Started");
        $status_stopped = $this->instanceStatusesRepository->findOneByStatus("Stopped");

        return $this->createQueryBuilder('i')
            ->where('i.instance_type = :instance_type')
            ->andWhere('(i.status = :status_started OR i.status = :status_stopped)')
            ->setParameter('status_started', $status_started->getId())
            ->setParameter('status_stopped', $status_stopped->getId())
            ->setParameter('instance_type', $instance_type->getId())
            ->getQuery()
            ->getResult()
        ;
    }
}
