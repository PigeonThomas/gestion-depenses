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
     * Find the last km recorded for a vidange (repa_vidange = true) for a given vehicle and user.
     * @param int $vehiculeId
     * @param int $userId
     * @return string|null
     */
    public function findLastVidangeKmByVehicule(int $vehiculeId, int $userId): ?string
    {
        $result = $this->createQueryBuilder('d')
            ->select('d.km_vehicule')
            ->where('d.vehicule = :vehiculeId')
            ->andWhere('d.user = :userId')
            ->andWhere('d.repa_vidange = true')
            ->andWhere('d.km_vehicule IS NOT NULL')
            ->setParameter('vehiculeId', $vehiculeId)
            ->setParameter('userId', $userId)
            ->orderBy('d.date_depense', 'DESC')
            ->addOrderBy('d.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['km_vehicule'] ?? null;
    }

    /**
     * Find the last km recorded for a distribution (repa_distribution = true) for a given vehicle and user.
     * @param int $vehiculeId
     * @param int $userId
     * @return string|null
     */
    public function findLastDistributionKmByVehicule(int $vehiculeId, int $userId): ?string
    {
        $result = $this->createQueryBuilder('d')
            ->select('d.km_vehicule')
            ->where('d.vehicule = :vehiculeId')
            ->andWhere('d.user = :userId')
            ->andWhere('d.repa_distribution = true')
            ->andWhere('d.km_vehicule IS NOT NULL')
            ->setParameter('vehiculeId', $vehiculeId)
            ->setParameter('userId', $userId)
            ->orderBy('d.date_depense', 'DESC')
            ->addOrderBy('d.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['km_vehicule'] ?? null;
    }

    /**
     * Find the last km recorded for a given vehicle across all dates (carburant or réparation categories).
     * Optionally excludes a specific depense (useful in edit context).
     * @param int $vehiculeId
     * @param int|null $excludeDepenseId ID of the depense to exclude (for edit)
     * @return string|null
     */
    public function findLastKmByVehicule(int $vehiculeId, ?int $excludeDepenseId = null): ?string
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d.km_vehicule')
            ->where('d.vehicule = :vehiculeId')
            ->andWhere('d.km_vehicule IS NOT NULL')
            ->andWhere('d.categorie IN (:categories)')
            ->setParameter('vehiculeId', $vehiculeId)
            ->setParameter('categories', [2, 10])
            ->orderBy('d.date_depense', 'DESC')
            ->addOrderBy('d.id', 'DESC')
            ->setMaxResults(1);

        if ($excludeDepenseId !== null) {
            $qb->andWhere('d.id != :excludeId')
               ->setParameter('excludeId', $excludeDepenseId);
        }

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result['km_vehicule'] ?? null;
    }

    /**
     * Find the km of the depense immediately preceding the given date, for the same vehicle and category.
     * Optionally excludes a specific depense (useful in edit context).
     * @param int $vehiculeId
     * @param int $categorieId
     * @param \DateTimeInterface $dateDepense
     * @param int|null $excludeDepenseId ID of the depense to exclude (for edit)
     * @return string|null
     */
    public function findPreviousKmByVehiculeAndCategorie(int $vehiculeId, int $categorieId, \DateTimeInterface $dateDepense, ?int $excludeDepenseId = null): ?string
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d.km_vehicule')
            ->where('d.vehicule = :vehiculeId')
            ->andWhere('d.categorie = :categorieId')
            ->andWhere('d.km_vehicule IS NOT NULL')
            ->andWhere('d.date_depense <= :dateDepense')
            ->setParameter('vehiculeId', $vehiculeId)
            ->setParameter('categorieId', $categorieId)
            ->setParameter('dateDepense', $dateDepense)
            ->orderBy('d.date_depense', 'DESC')
            ->addOrderBy('d.id', 'DESC')
            ->setMaxResults(1);

        if ($excludeDepenseId !== null) {
            $qb->andWhere('d.id != :excludeId')
               ->setParameter('excludeId', $excludeDepenseId);
        }

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result['km_vehicule'] ?? null;
    }

    /**
     * Find the km of the depense immediately following the given date, for the same vehicle and category.
     * Optionally excludes a specific depense (useful in edit context).
     * @param int $vehiculeId
     * @param int $categorieId
     * @param \DateTimeInterface $dateDepense
     * @param int|null $excludeDepenseId ID of the depense to exclude (for edit)
     * @return string|null
     */
    public function findNextKmByVehiculeAndCategorie(int $vehiculeId, int $categorieId, \DateTimeInterface $dateDepense, ?int $excludeDepenseId = null): ?string
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d.km_vehicule')
            ->where('d.vehicule = :vehiculeId')
            ->andWhere('d.categorie = :categorieId')
            ->andWhere('d.km_vehicule IS NOT NULL')
            ->andWhere('d.date_depense >= :dateDepense')
            ->setParameter('vehiculeId', $vehiculeId)
            ->setParameter('categorieId', $categorieId)
            ->setParameter('dateDepense', $dateDepense)
            ->orderBy('d.date_depense', 'ASC')
            ->addOrderBy('d.id', 'ASC')
            ->setMaxResults(1);

        if ($excludeDepenseId !== null) {
            $qb->andWhere('d.id != :excludeId')
               ->setParameter('excludeId', $excludeDepenseId);
        }

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result['km_vehicule'] ?? null;
    }

    /**
     * Find last km for a given vehicle for a given year for carburant or reparation expenses (id 2 and 10) by user
     * @param int $vehiculeId The ID of the vehicle to find the last Km for.
     * @param int $annee The year of the month to find the last Km for.
     * @param int $userId The ID of the user to find the last Km for.
     * @return int|null The last Km for the specified vehicle and year.
     */
    public function findLastKmByVehiculeAndYear(int $vehiculeId, int $annee, int $userId): ?int
    {
        $start = new \DateTimeImmutable(sprintf('%04d-01-01', $annee));
        $end = $start->modify('first day of January next year');    
        $result = $this->createQueryBuilder('d')
            ->select('d.km_vehicule')
            ->where('d.date_depense >= :start')
            ->andWhere('d.date_depense < :end')
            ->andWhere('d.categorie IN (:categories)')
            ->andWhere('d.vehicule = :vehiculeId')
            ->andWhere('d.user = :userId')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('categories', [2, 10])
            ->setParameter('vehiculeId', $vehiculeId)
            ->setParameter('userId', $userId)
            ->orderBy('d.date_depense', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['km_vehicule'] ?? null;
    }

    /**
     * Find last Km for a given vehicle for a given month for carburant or reparation expenses (id 2 and 10) by User.
     * @param int $vehiculeId The ID of the vehicle to find the last Km for.
     * @param int $annee The year of the month to find the last Km for.
     * @param int $mois The month to find the last Km for (1-12).
     * @param int $userId The ID of the user to find the last Km for.
     * @return string|null The last Km for the specified vehicle and month.
     */
    public function findLastKmByVehiculeAndMonth(int $vehiculeId, int $annee, int $mois, int $userId): ?string
    {
        $start = new \DateTimeImmutable(sprintf('%04d-%02d-01', $annee, $mois));
        $end = $start->modify('first day of next month');

        $result = $this->createQueryBuilder('d')
            ->select('d.km_vehicule')
            ->where('d.date_depense >= :start')
            ->andWhere('d.date_depense < :end')
            ->andWhere('d.categorie IN (:categories)')
            ->andWhere('d.vehicule = :vehiculeId')
            ->andWhere('d.user = :userId')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('categories', [2, 10])
            ->setParameter('vehiculeId', $vehiculeId)
            ->setParameter('userId', $userId)
            ->orderBy('d.date_depense', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['km_vehicule'] ?? null;
    }

    /**
     * Find the last known Km for a given vehicle recorded strictly before a given month (carburant or reparation expenses).
     * Used as a fallback to bridge gaps of several months without any recorded expense.
     * @param int $vehiculeId The ID of the vehicle to find the last Km for.
     * @param int $annee The year of the month used as the upper bound.
     * @param int $mois The month used as the upper bound (1-12).
     * @param int $userId The ID of the user to find the last Km for.
     * @return string|null The last known Km before the specified month.
     */
    public function findLastKmByVehiculeBeforeMonth(int $vehiculeId, int $annee, int $mois, int $userId): ?string
    {
        $start = new \DateTimeImmutable(sprintf('%04d-%02d-01', $annee, $mois));

        $result = $this->createQueryBuilder('d')
            ->select('d.km_vehicule')
            ->where('d.date_depense < :start')
            ->andWhere('d.categorie IN (:categories)')
            ->andWhere('d.vehicule = :vehiculeId')
            ->andWhere('d.user = :userId')
            ->setParameter('start', $start)
            ->setParameter('categories', [2, 10])
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

    /**
     * Return the last repair expense entry for a vehicle and a user.
     * @param int $vehiculeId The ID of the vehicle.
     * @param int $userId The ID of the user.
     * @return Depense|null The latest repair expense entity, or null if none found.
     */
    public function findLastReparationByVehiculeAndUser(int $vehiculeId, int $userId): ?Depense
    {
        return $this->createQueryBuilder('d')
            ->where('d.vehicule = :vehiculeId')
            ->andWhere('d.user = :userId')
            ->andWhere('d.categorie = :categorie')
            ->setParameter('vehiculeId', $vehiculeId)
            ->setParameter('userId', $userId)
            ->setParameter('categorie', 2)
            ->orderBy('d.date_depense', 'DESC')
            ->addOrderBy('d.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Return the last vidange expense entry for a vehicle and a user.
     * @param int $vehiculeId The ID of the vehicle.
     * @param int $userId The ID of the user.
     * @return Depense|null The latest vidange expense entity, or null if none found.
     */
    public function findLastVidangeByVehiculeAndUser(int $vehiculeId, int $userId): ?Depense
    {
        return $this->createQueryBuilder('d')
            ->where('d.vehicule = :vehiculeId')
            ->andWhere('d.user = :userId')
            ->andWhere('d.categorie = :categorie')
            ->andWhere('d.repa_vidange = :vidange')
            ->setParameter('vehiculeId', $vehiculeId)
            ->setParameter('userId', $userId)
            ->setParameter('categorie', 2) // Assuming 2 is the ID for reparation category
            ->setParameter('vidange', true) // Assuming 'repa_vidange' is a boolean indicating vidange
            ->orderBy('d.date_depense', 'DESC')
            ->addOrderBy('d.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    /**
     * Return the last distribution expense entry for a vehicle and a user.
     * @param int $vehiculeId The ID of the vehicle.
     * @param int $userId The ID of the user.
     * @return Depense|null The latest distribution expense entity, or null if none found.
     */
    public function findLastDistributionByVehiculeAndUser(int $vehiculeId, int $userId): ?Depense
    {
        return $this->createQueryBuilder('d')
            ->where('d.vehicule = :vehiculeId')
            ->andWhere('d.user = :userId')
            ->andWhere('d.categorie = :categorie')
            ->andWhere('d.repa_distribution = :distribution')
            ->setParameter('vehiculeId', $vehiculeId)
            ->setParameter('userId', $userId)
            ->setParameter('categorie', 2) // Assuming 2 is the ID for reparation category
            ->setParameter('distribution', true) // Assuming 'repa_distribution' is a boolean indicating distribution
            ->orderBy('d.date_depense', 'DESC')
            ->addOrderBy('d.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Return the last distribution date for a vehicle and a user.
     * @param int $vehiculeId The ID of the vehicle.
     * @param int $userId The ID of the user.
     * @return \DateTimeInterface|null The latest distribution date, or null if none found.
     */
    public function findLastDistributionDateByVehiculeAndUser(int $vehiculeId, int $userId): ?\DateTimeInterface
    {
        $result = $this->createQueryBuilder('d')
            ->select('d.date_depense')
            ->where('d.vehicule = :vehiculeId')
            ->andWhere('d.user = :userId')
            ->andWhere('d.categorie = :categorie')
            ->andWhere('d.repa_distribution = :distribution')
            ->setParameter('vehiculeId', $vehiculeId)
            ->setParameter('userId', $userId)
            ->setParameter('categorie', 2) // Assuming 2 is the ID for reparation category
            ->setParameter('distribution', true) // Assuming 'repa_distribution' is a boolean indicating distribution
            ->orderBy('d.date_depense', 'DESC')
            ->addOrderBy('d.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result ? $result['date_depense'] : null;
    }

    /**
     * Return the last contrôle technique expense entry for a vehicle and a user.
     * @param int $vehiculeId The ID of the vehicle.
     * @param int $userId The ID of the user.
     * @return Depense|null The latest contrôle technique expense entity, or null if none found.
     */
    public function findLastControleTechniqueByVehiculeAndUser(int $vehiculeId, int $userId): ?Depense
    {
        return $this->createQueryBuilder('d')
            ->where('d.vehicule = :vehiculeId')
            ->andWhere('d.user = :userId')
            ->andWhere('d.categorie = :categorie')
            ->andWhere('d.repa_CT = :controleTechnique')
            ->setParameter('vehiculeId', $vehiculeId)
            ->setParameter('userId', $userId)
            ->setParameter('categorie', 2) // Assuming 2 is the ID for reparation category
            ->setParameter('controleTechnique', true) // Assuming 'repa_CT' is a boolean indicating contrôle technique
            ->orderBy('d.date_depense', 'DESC')
            ->addOrderBy('d.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Return the last contrôle technique date for a vehicle and a user.
     * @param int $vehiculeId The ID of the vehicle.
     * @param int $userId The ID of the user.
     * @return \DateTimeInterface|null The latest contrôle technique date, or null if none found.
     */
    public function findLastControleTechniqueDateByVehiculeAndUser(int $vehiculeId, int $userId): ?\DateTimeInterface
    {
        $result = $this->createQueryBuilder('d')
            ->select('d.date_depense')
            ->where('d.vehicule = :vehiculeId')
            ->andWhere('d.user = :userId')
            ->andWhere('d.categorie = :categorie')
            ->andWhere('d.repa_CT = :controleTechnique')
            ->setParameter('vehiculeId', $vehiculeId)
            ->setParameter('userId', $userId)
            ->setParameter('categorie', 2) // Assuming 2 is the ID for reparation category
            ->setParameter('controleTechnique', true) // Assuming 'repa_CT' is a boolean indicating contrôle technique
            ->orderBy('d.date_depense', 'DESC')
            ->addOrderBy('d.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result ? $result['date_depense'] : null;
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
