<?php

namespace App\Repository;

use Psr\Log\LoggerInterface;

use App\Entity\TaskTechs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskTechs>
 *
 * @method TaskTechs|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskTechs|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskTechs[]    findAll()
 * @method TaskTechs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskTechsRepository extends ServiceEntityRepository
{
    private $logger;
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->logger = $logger;

        $this->logger->debug(__METHOD__);

        parent::__construct($registry, TaskTechs::class);
    }

    public function add(TaskTechs $entity, bool $flush = false): void
    {
        $this->logger->debug(__METHOD__);

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TaskTechs $entity, bool $flush = false): void
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

        return $this->findBy(array(), array('tech' => 'ASC', 'task' => 'ASC'));
    }

//    /**
//     * @return TaskTechs[] Returns an array of TaskTechs objects
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

//    public function findOneBySomeField($value): ?TaskTechs
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
