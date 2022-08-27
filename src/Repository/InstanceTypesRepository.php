<?php

namespace App\Repository;

use App\Entity\InstanceTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InstanceTypes>
 *
 * @method InstanceTypes|null find($id, $lockMode = null, $lockVersion = null)
 * @method InstanceTypes|null findOneBy(array $criteria, array $orderBy = null)
 * @method InstanceTypes[]    findAll()
 * @method InstanceTypes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstanceTypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InstanceTypes::class);
    }

    public function add(InstanceTypes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(InstanceTypes $entity, bool $flush = false): void
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
	$qb = $this->createQueryBuilder('it');

	$qb->delete();

	return $qb->getQuery()->getSingleScalarResult() ?? 0;
    }

//    /**
//     * @return InstanceTypes[] Returns an array of InstanceTypes objects
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

//    public function findOneBySomeField($value): ?InstanceTypes
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
