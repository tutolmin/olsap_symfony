<?php

namespace App\Repository;

use Psr\Log\LoggerInterface;

use App\Entity\TaskOses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

/**
 * @extends ServiceEntityRepository<TaskOses>
 *
 * @method TaskOses|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskOses|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskOses[]    findAll()
 * @method TaskOses[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskOsesRepository extends ServiceEntityRepository
{
    private LoggerInterface $logger;
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->logger = $logger;

        $this->logger->debug(__METHOD__);

        parent::__construct($registry, TaskOses::class);
    }

    public function add(TaskOses $entity, bool $flush = false): bool
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

    public function remove(TaskOses $entity, bool $flush = false): void
    {
        $this->logger->debug(__METHOD__);

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return void
     */
    public function deleteAll(): void
    {
        $this->logger->debug(__METHOD__);

        $qb = $this->createQueryBuilder('to')->delete();

        $qb->getQuery()->execute();

//      return $qb->getQuery()->getSingleScalarResult() ?? 0;
    }

//    /**
//     * @return TaskOses[] Returns an array of TaskOses objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TaskOses
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
