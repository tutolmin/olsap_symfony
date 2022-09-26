<?php

namespace App\Repository;

use Psr\Log\LoggerInterface;

use App\Entity\Testees;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Testees>
 *
 * @method Testees|null find($id, $lockMode = null, $lockVersion = null)
 * @method Testees|null findOneBy(array $criteria, array $orderBy = null)
 * @method Testees[]    findAll()
 * @method Testees[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TesteesRepository extends ServiceEntityRepository
{
    private $logger;
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->logger = $logger;

        $this->logger->debug(__METHOD__);

        parent::__construct($registry, Testees::class);
        $this->logger = $logger;

    }

    public function add(Testees $entity, bool $flush = false): void
    {
        $this->logger->debug(__METHOD__);

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Testees $entity, bool $flush = false): void
    {
        $this->logger->debug(__METHOD__);

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Testees[] Returns an array of Testees objects
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

//    public function findOneBySomeField($value): ?Testees
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
