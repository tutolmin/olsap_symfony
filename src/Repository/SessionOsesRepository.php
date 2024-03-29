<?php

namespace App\Repository;

use Psr\Log\LoggerInterface;

use App\Entity\SessionOses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SessionOses>
 *
 * @method SessionOses|null find($id, $lockMode = null, $lockVersion = null)
 * @method SessionOses|null findOneBy(array $criteria, array $orderBy = null)
 * @method SessionOses[]    findAll()
 * @method SessionOses[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionOsesRepository extends ServiceEntityRepository
{
    private $logger;
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->logger->debug(__METHOD__);

        parent::__construct($registry, SessionOses::class);
    }

    public function add(SessionOses $entity, bool $flush = false): void
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

    public function remove(SessionOses $entity, bool $flush = false): void
    {
        $this->logger->debug(__METHOD__);

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SessionOses[] Returns an array of SessionOses objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SessionOses
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
