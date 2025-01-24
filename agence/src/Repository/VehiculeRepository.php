<?php

namespace App\Repository;

use App\Entity\Vehicule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicule>
 */
class VehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicule::class);
    }

    public function findAllWithReservationCount(): array
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.reservations', 'r')
            ->addSelect('COUNT(r.id) AS reservationCount')
            ->groupBy('v.id')
            ->getQuery()
            ->getResult();
    }

    public function findWithReservationCount(int $id): ?array
    {
        return $this->createQueryBuilder('v')
            ->select('v', 'COUNT(r.id) AS reservationCount')
            ->leftJoin('v.reservations', 'r')
            ->where('v.id = :id')
            ->setParameter('id', $id)
            ->groupBy('v.id')
            ->getQuery()
            ->getOneOrNullResult();
    }



    //    /**
    //     * @return Vehicule[] Returns an array of Vehicule objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Vehicule
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
