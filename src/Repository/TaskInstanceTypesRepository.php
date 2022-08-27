<?php

namespace App\Repository;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskInstanceTypes::class);
    }

    public function add(TaskInstanceTypes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TaskInstanceTypes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return int
     */
    public function deleteAll(): int
    {
        $qb = $this->createQueryBuilder('tt');

        $qb->delete();

        return $qb->getQuery()->getSingleScalarResult() ?? 0;
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
