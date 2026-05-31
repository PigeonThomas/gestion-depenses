<?php

namespace App\Repository;

use App\Entity\Depense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Depense>
 */
class DepenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depense::class);
    }

    /**
     * Return a query of expenses for a given user, ready to be paginated.
     * @param int $userId The ID of the user to retrieve expenses for.
     * @return Query
     */
    public function queryByUserId(int $userId): Query
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.categorie', 'c')
            ->addSelect('c')
            ->andWhere('d.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('d.date_depense', 'DESC')
            ->getQuery();
    }

    /**
     * Return the total expenses amount for a given month by User.
     * @param int $annee The year of the month to calculate the total for.
     * @param int $mois The month to calculate the total for (1-12).
     * @return string The total expenses amount for the specified month.
     */
    public function findTotalDepenseByMonth(int $annee, int $mois, int $userId): string
    {
        
        $start = new \DateTimeImmutable(sprintf('%04d-%02d-01', $annee, $mois));
        $end = $start->modify('first day of next month');

        return (string) $this->createQueryBuilder('d')
            ->select('COALESCE(SUM(d.montant_depense), 0)')
            ->where('d.date_depense >= :start')
            ->andWhere('d.date_depense < :end')
            ->andWhere('d.user = :userId')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }

        /**
        * Return the total essence expenses amount for a given month by User.
        * @param int $annee The year of the month to calculate the total for.
        * @param int $mois The month to calculate the total for (1-12).
        * @return string The total essence expenses amount for the specified month.
        */
    public function findTotalEssenceByMonth(int $annee, int $mois, int $userId): string
    {
        $start = new \DateTimeImmutable(sprintf('%04d-%02d-01', $annee, $mois));
        $end = $start->modify('first day of next month');

        return (string) $this->createQueryBuilder('d')
            ->select('COALESCE(SUM(d.montant_depense), 0)')
            ->where('d.date_depense >= :start')
            ->andWhere('d.date_depense < :end')
            ->andWhere('d.categorie = :categorie')
            ->andWhere('d.user = :userId')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('categorie', '10') // Adjust the category name as needed
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Return the total reparation expenses amount for a given month by User.
     * @param int $annee The year of the month to calculate the total for.
     * @param int $mois The month to calculate the total for (1-12).
     * @return int The total reparation expenses amount for the specified month.
     */
    public function findTotalReparationByMonth(int $annee, int $mois, int $userId): int
    {
        $start = new \DateTimeImmutable(sprintf('%04d-%02d-01', $annee, $mois));
        $end = $start->modify('first day of next month');

        return (int) $this->createQueryBuilder('d')
            ->select('COALESCE(SUM(d.montant_depense), 0)')
            ->where('d.date_depense >= :start')
            ->andWhere('d.date_depense < :end')
            ->andWhere('d.categorie = :categorie') 
            ->andWhere('d.user = :userId')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('categorie', '2') // Adjust the category name as needed
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Find last Km for a given vehicle for a given month for carbuant expenses (id : 10) by User.
     * @param int $vehiculeId The ID of the vehicle to find the last Km for.
     * @param int $annee The year of the month to find the last Km for.
     * @param int $mois The month to find the last Km for (1-12).
     * @return string|null The last Km for the specified vehicle and month.
     */
    public function findLastKmByVehiculeAndMonth(int $vehiculeId, int $annee, int $mois, int $userId): ?string
    {
        $start = new \DateTimeImmutable(sprintf('%04d-%02d-01', $annee, $mois));
        $end = $start->modify('first day of next month');

        $result = $this->createQueryBuilder('d')
            ->select('d.km_vehicule') // Assuming the Km is stored in the km_vehicule field
            ->where('d.date_depense >= :start')
            ->andWhere('d.date_depense < :end')
            ->andWhere('d.categorie = :categorie') // Assuming 'carbuant' is the category name for fuel expenses
            ->andWhere('d.vehicule = :vehiculeId')
            ->andWhere('d.user = :userId')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('categorie', '10') // Adjust the category name as needed
            ->setParameter('vehiculeId', $vehiculeId)
            ->setParameter('userId', $userId)
            ->orderBy('d.date_depense', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['km_vehicule'] ?? null;
    }

    /**
     * Fonction pour trouver toutes les dépenses d'un utilisateur spécifique
     * @param int $userId
     * @return Depense[]
     */
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('d.date_depense', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Return the total categories expenses amount for a given month by User
     * @param int $annee The year of the month to calculate the total for.
     * @param int $mois The month to calculate the total for (1-12).
     * @param int $userId The ID of the user to calculate the total for.
     * @return array An array of total expenses amount for each category for the specified month.
     */
    public function findTotalByCategoryAndMonth(int $annee, int $mois, int $userId): array
    {
        $start = new \DateTimeImmutable(sprintf('%04d-%02d-01', $annee, $mois));
        $end = $start->modify('first day of next month');
        return $this->createQueryBuilder('d')
            ->select('c.nom_categorie AS category', 'COALESCE(SUM(d.montant_depense), 0) AS total', 'c.couleur_categorie AS color')
            ->join('d.categorie', 'c')
            ->where('d.date_depense >= :start')
            ->andWhere('d.date_depense < :end')
            ->andWhere('d.user = :userId')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('userId', $userId)
            ->groupBy('c.nom_categorie', 'c.couleur_categorie')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Return the total magasins expenses amount for a given month by User
     * @param int $annee The year of the month to calculate the total for.
     * @param int $mois The month to calculate the total for (1-12).
     * @param int $userId The ID of the user to calculate the total for.
     * @return array An array of total expenses amount for each magasin for the specified month.
     */
    public function findTotalByMagasinAndMonth(int $annee, int $mois, int $userId): array
    {
        $start = new \DateTimeImmutable(sprintf('%04d-%02d-01', $annee, $mois));
        $end = $start->modify('first day of next month');
        return $this->createQueryBuilder('d')
            ->select('m.nom_magasin AS magasin', 'COALESCE(SUM(d.montant_depense), 0) AS total')
            ->join('d.magasin', 'm')
            ->where('d.date_depense >= :start')
            ->andWhere('d.date_depense < :end')
            ->andWhere('d.user = :userId')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('userId', $userId)
            ->groupBy('m.nom_magasin')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Depense[] Returns an array of Depense objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Depense
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
