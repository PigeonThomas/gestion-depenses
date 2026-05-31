<?php

namespace App\Repository;

use App\Entity\Magasin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Magasin>
 */
class MagasinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Magasin::class);
    }

    /**
     * Return a query of Magasins for a given user, ready to be paginated.
     * @param int $userId The ID of the user to retrieve magasins for.
     * @return Query
     */
    public function queryByUserId(int $userId): Query
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery();
    }

    /**
     * Fonction pour trouver tous les magasins d'un utilisateur spécifique
     * @param int $userId
     * @return Magasin[]
     */
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Magasin[] Returns an array of Magasin objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Magasin
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
