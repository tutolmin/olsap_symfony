<?php

namespace App\Repository;

use App\Entity\Instances;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\InstanceStatuses;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @extends ServiceEntityRepository<Instances>
 *
 * @method Instances|null find($id, $lockMode = null, $lockVersion = null)
 * @method Instances|null findOneBy(array $criteria, array $orderBy = null)
 * @method Instances[]    findAll()
 * @method Instances[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstancesRepository extends ServiceEntityRepository
{
    private $instanceStatusesRepository;
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Instances::class);

        $this->entityManager = $em;

        $this->instanceStatusesRepository = $this->entityManager->getRepository( InstanceStatuses::class);
    }

    public function add(Instances $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Instances $entity, bool $flush = false): void
    {
	// Fetch all linked Addresses and release them
	$addresses = $entity->getAddresses();
	foreach($addresses as $address)
	  $address->setInstance(null);

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByTypeAndStatus($instance_type, $status_string): ?Instances
    {
        $status = $this->instanceStatusesRepository->findOneByStatus($status_string);

	// TODO: check for valid result

        return $this->createQueryBuilder('i')
            ->where('i.status = :status')
            ->andWhere('i.instance_type = :instance_type')
            ->setParameter('status', $status->getId())
            ->setParameter('instance_type', $instance_type->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return Instances[] Returns an array of Instances objects
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

//    public function findOneBySomeField($value): ?Instances
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
