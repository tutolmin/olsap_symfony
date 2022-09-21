<?php

namespace App\Repository;

use App\Entity\Environments;
use App\Entity\EnvironmentStatuses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Environments>
 *
 * @method Environments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Environments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Environments[]    findAll()
 * @method Environments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnvironmentsRepository extends ServiceEntityRepository
{
    private $entityManager;
    private $environmentStatusesRepository;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Environments::class);

	$this->entityManager = $entityManager;

        $this->environmentStatusesRepository = $this->entityManager->getRepository( EnvironmentStatuses::class);
    }

    public function add(Environments $entity, bool $flush = false): void
    {
	// TODO: check if the instance has been used already in another env
	$timestamp = new \DateTimeImmutable('NOW');
	$entity->setHash(substr(md5($timestamp->format('Y-m-d H:i:s')),0,8));
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Environments $entity, bool $flush = false): void
    {
	// TODO: change status, recover init snapshot, etc.

        // Fetch linked Instances and release them
	$entity->setInstance(null);

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Environments[] Returns an array of Environments objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Environments
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findOneDeployed($task_id): ?Environments
    {
        $env_status = $this->environmentStatusesRepository->findOneByStatus("Deployed");

        return $this->createQueryBuilder('e')
            ->where('e.session is null')
            ->andWhere('e.task = :task_id')
            ->andWhere('e.status = :status_id')
            ->setParameter('task_id', $task_id)
            ->setParameter('status_id', $env_status->getId())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
