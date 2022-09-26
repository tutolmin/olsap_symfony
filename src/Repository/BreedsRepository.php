<?php

namespace App\Repository;

use Psr\Log\LoggerInterface;

use App\Entity\Breeds;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Breeds>
 *
 * @method Breeds|null find($id, $lockMode = null, $lockVersion = null)
 * @method Breeds|null findOneBy(array $criteria, array $orderBy = null)
 * @method Breeds[]    findAll()
 * @method Breeds[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BreedsRepository extends ServiceEntityRepository
{
    private $logger;
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

	parent::__construct($registry, Breeds::class);
    }

    public function add(Breeds $entity, bool $flush = false): void
    {
        $this->logger->debug(__METHOD__);

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Breeds $entity, bool $flush = false): void
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

        return $this->findBy(array(), array('name' => 'ASC'));
    }

//    /**
//     * @return Breeds[] Returns an array of Breeds objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Breeds
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
