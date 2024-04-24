<?php

namespace App\Repository;

use Psr\Log\LoggerInterface;

use App\Entity\InstanceStatuses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

/**
 * @extends ServiceEntityRepository<InstanceStatuses>
 *
 * @method InstanceStatuses|null find($id, $lockMode = null, $lockVersion = null)
 * @method InstanceStatuses|null findOneBy(array $criteria, array $orderBy = null)
 * @method InstanceStatuses[]    findAll()
 * @method InstanceStatuses[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstanceStatusesRepository extends ServiceEntityRepository
{
    private LoggerInterface $logger;
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        parent::__construct($registry, InstanceStatuses::class);
        $this->logger = $logger;

    }

    public function add(InstanceStatuses $entity, bool $flush = false): bool
    {
        $this->logger->debug(__METHOD__);

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            try {
                $this->getEntityManager()->flush();
            } catch (UniqueConstraintViolationException $e) {
                $this->logger->error("Attempted to insert duplicate item.");
                return false;
            } catch (NotNullConstraintViolationException $e) {
                $this->logger->error("Mandatory parameter has NOT been set.");
                return false;
            }
        }
        return true;
    }

    public function remove(InstanceStatuses $entity, bool $flush = false): bool
    {
        $this->logger->debug(__METHOD__);

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
        return true;
    }

//    /**
//     * @return InstanceStatuses[] Returns an array of InstanceStatuses objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InstanceStatuses
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
