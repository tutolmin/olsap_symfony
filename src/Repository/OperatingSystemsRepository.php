<?php

namespace App\Repository;

use Psr\Log\LoggerInterface;

use App\Entity\OperatingSystems;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OperatingSystems>
 *
 * @method OperatingSystems|null find($id, $lockMode = null, $lockVersion = null)
 * @method OperatingSystems|null findOneBy(array $criteria, array $orderBy = null)
 * @method OperatingSystems[]    findAll()
 * @method OperatingSystems[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperatingSystemsRepository extends ServiceEntityRepository
{
    private $logger;
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->logger = $logger;

        $this->logger->debug(__METHOD__);

        parent::__construct($registry, OperatingSystems::class);
    }

    public function add(OperatingSystems $entity, bool $flush = false): void
    {
        $this->logger->debug(__METHOD__);

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OperatingSystems $entity, bool $flush = false): void
    {
        $this->logger->debug(__METHOD__);

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAll()
    {
        $this->logger->debug(__METHOD__);

        return $this->findBy(array(), array('breed' => 'ASC', 'release' => 'ASC'));
    }

//    /**
//     * @return OperatingSystems[] Returns an array of OperatingSystems objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?OperatingSystems
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
