<?php

namespace App\Repository;

use App\Entity\HardwareProfiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HardwareProfiles>
 *
 * @method HardwareProfiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method HardwareProfiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method HardwareProfiles[]    findAll()
 * @method HardwareProfiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HardwareProfilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HardwareProfiles::class);
    }

    public function add(HardwareProfiles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(HardwareProfiles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return HardwareProfiles[] Returns an array of HardwareProfiles objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HardwareProfiles
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
