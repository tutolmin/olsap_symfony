<?php

namespace App\Repository;

use Psr\Log\LoggerInterface;

use App\Entity\TaskInstanceTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskInstanceTypes>
 *
 * @method TaskInstanceTypes|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskInstanceTypes|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskInstanceTypes[]    findAll()
 * @method TaskInstanceTypes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskInstanceTypesRepository extends ServiceEntityRepository
{
    private LoggerInterface $logger;
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->logger = $logger;

        $this->logger->debug(__METHOD__);

        parent::__construct($registry, TaskInstanceTypes::class);
    }

    public function add(TaskInstanceTypes $entity, bool $flush = false): void
    {
        $this->logger->debug(__METHOD__);

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            try {
                $this->getEntityManager()->flush();
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
//                echo "Exception Found - " . $ex->getMessage() . "<br/>";
            }
        }
    }

    public function remove(TaskInstanceTypes $entity, bool $flush = false): void
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

        $qb = $this->createQueryBuilder('tt');

        $qb->delete();

//        return $qb->getQuery()->getSingleScalarResult() ?? 0;
    }

//    /**
//     * @return TaskInstanceTypes[] Returns an array of TaskInstanceTypes objects
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

//    public function findOneBySomeField($value): ?TaskInstanceTypes
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
