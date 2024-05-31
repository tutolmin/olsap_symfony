<?php

namespace App\Repository;

use Psr\Log\LoggerInterface;

use App\Entity\Tasks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
//use Doctrine\ORM\EntityManagerInterface;
//use App\Entity\Environments;
//use App\Repository\EnvironmentsRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

/**
 * @extends ServiceEntityRepository<Tasks>
 *
 * @method Tasks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tasks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tasks[]    findAll()
 * @method Tasks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TasksRepository extends ServiceEntityRepository
{
    private LoggerInterface $logger;

    public function __construct(ManagerRegistry $registry,
            LoggerInterface $logger)
    {
        $this->logger = $logger;

        $this->logger->debug(__METHOD__);

        parent::__construct($registry, Tasks::class);

//        $this->entityManager = $entityManager;

//        $this->environmentRepository = $entityManager->getRepository( Environments::class);
    }

    public function add(Tasks $entity, bool $flush = false): bool
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

    public function remove(Tasks $entity, bool $flush = false): void
    {
        $this->logger->debug(__METHOD__);
/*
        // Fetch all linked Environments and delete them
        $envs = $entity->getEnvs();

        foreach($envs as $env)
            $this->environmentRepository->remove($env);
*/

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        } 
    }

    /**
     * 
     * @return array<Tasks>
     */
    public function findAll()
    {
        $this->logger->debug(__METHOD__);

        return $this->findBy(array(), array('name' => 'ASC'));
    }

//    /**
//     * @return Tasks[] Returns an array of Tasks objects
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

//    public function findOneBySomeField($value): ?Tasks
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
