<?php

namespace App\Repository;

use Psr\Log\LoggerInterface;

use App\Entity\TaskTechs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

/**
 * @extends ServiceEntityRepository<TaskTechs>
 *
 * @method TaskTechs|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskTechs|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskTechs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskTechsRepository extends ServiceEntityRepository
{
    private LoggerInterface $logger;
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->logger = $logger;

        $this->logger->debug(__METHOD__);

        parent::__construct($registry, TaskTechs::class);
    }

    public function add(TaskTechs $entity, bool $flush = false): bool
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

    public function remove(TaskTechs $entity, bool $flush = false): void
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

        $qb = $this->createQueryBuilder('tt')->delete();

        $qb->getQuery()->execute();

//      return $qb->getQuery()->getSingleScalarResult() ?? 0;
    }
    
    /**
     * 
     * @return array<TaskTechs>
     */
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
