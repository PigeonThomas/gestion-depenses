<?php

namespace App\Repository;

use App\Entity\Vehicule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Vehicule>
 */
class VehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicule::class);
    }

    /**
     * Return a query of Vehicules for a given user, ready to be paginated.
     * @param int $userId The ID of the user to retrieve vehicules for.
     * @return Query
     */
    public function queryByUserId(int $userId): Query
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('v.createdAt', 'DESC')
            ->getQuery();
    }

    /**
     * find initial km of vehicule
     *
     * @param int $vehiculeId The ID of the vehicle to find the initial Km for.
     * @return string|null The initial Km for the specified vehicle.
     */
    public function findInitialKmByVehicule(int $vehiculeId) : ?string
    {
        $vehicule = $this->find($vehiculeId);

        return $vehicule?->getKmAchatVehicule();
    }


    /**
     * Fonction pour trouver tous les véhicules d'un utilisateur spécifique
     * @param int $userId
     * @return Vehicule[]
     */
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('v.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
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
