<?php

namespace App\Service;

use App\Repository\DepenseRepository;
use App\Repository\VehiculeRepository;

class DistanceVehiculeService
{
    private DepenseRepository $depenseRepository;
    private VehiculeRepository $vehiculeRepository;

    public function __construct(DepenseRepository $depenseRepository, VehiculeRepository $vehiculeRepository)
    {
        $this->depenseRepository = $depenseRepository;
        $this->vehiculeRepository = $vehiculeRepository;
    }

    /**
     * Calcul of distance made during a month for a vehicule
     *
     * @param integer $vehiculeId
     * @param integer $annee
     * @param integer $mois
     * @return string
     */
    public function calculateDistance(int $vehiculeId, int $annee, int $mois, int $userId): string
    {
        $totalKmOfMonth = '0';

        //condition to get the previous month and year if the current month is January
        $moisPrecedent = $mois === 1 ? 12 : $mois - 1;
        $anneePrecedente = $mois === 1 ? $annee - 1 : $annee;

        $lastKmActualMonth = $this->depenseRepository->findLastKmByVehiculeAndMonth($vehiculeId, $annee, $mois, $userId);
        $lastKmLastMonth = $this->depenseRepository->findLastKmByVehiculeAndMonth($vehiculeId, $anneePrecedente, $moisPrecedent, $userId);

        if ($lastKmActualMonth === null || $lastKmActualMonth === '') {
            return $totalKmOfMonth; // Return 0 if there are no distance records for the month
        }

        if ($lastKmLastMonth === null || $lastKmLastMonth === '') {
            $lastKmLastMonth = $this->vehiculeRepository->findInitialKmByVehicule($vehiculeId); // Use initial Km if there are no records for the previous month
        }
        $totalKmOfMonth = (string) ($lastKmActualMonth - $lastKmLastMonth);
        return $totalKmOfMonth;
    }

    /**
     * Calcul of distance made during a year for a vehicule
     * 
     * @param integer $vehiculeId
     * @param integer $annee
     * @return integer
     */
    public function calculateDistanceYear(int $vehiculeId, int $annee, int $userId): int
    {
        $totalKmOfYear = 0;

        $lastKmOfYear = $this->depenseRepository->findLastKmByVehiculeAndYear($vehiculeId, $annee, $userId);
        $lastKmOfPreviousYear = $this->depenseRepository->findLastKmByVehiculeAndYear($vehiculeId, $annee - 1, $userId);

        if ($lastKmOfYear === null || $lastKmOfYear === '') {
            return $totalKmOfYear; // Return 0 if there are no distance records for the year
        }

        if ($lastKmOfPreviousYear === null || $lastKmOfPreviousYear === '') {
            $lastKmOfPreviousYear = $this->vehiculeRepository->findInitialKmByVehicule($vehiculeId); // Use initial Km if there are no records for the previous year
        }
        $totalKmOfYear = (int) ($lastKmOfYear - $lastKmOfPreviousYear);
        return $totalKmOfYear;
    }

    /**
     * Calcul of distance made since the purchase of a vehicule
     * @param integer $vehiculeId
     * @param integer $userId
     * @return integer
     */
    public function calculateDistanceSincePurchase(int $vehiculeId, int $userId): int
    {
        $totalKmSincePurchase = 0;
        $lastKm = $this->depenseRepository->findLastKmByVehicule($vehiculeId, $userId);
        if ($lastKm === null || $lastKm === '') {
            $lastKm = $this->vehiculeRepository->findInitialKmByVehicule($vehiculeId);
        }
        $totalKmSincePurchase = (int) ($lastKm - $this->vehiculeRepository->findInitialKmByVehicule($vehiculeId));
        return $totalKmSincePurchase;
    }
}