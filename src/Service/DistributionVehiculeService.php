<?php

namespace App\Service;

use App\Repository\DepenseRepository;
use App\Repository\VehiculeRepository;

class DistributionVehiculeService
{
    private DepenseRepository $depenseRepository;
    private VehiculeRepository $vehiculeRepository;

    public function __construct(DepenseRepository $depenseRepository, VehiculeRepository $vehiculeRepository)
    {
        $this->depenseRepository = $depenseRepository;
        $this->vehiculeRepository = $vehiculeRepository;
    }

    /**
     * Calcul of next km to maintenance distribution for a vehicule
     *
     * @param integer $vehiculeId
     * @param integer $userId
     * @return int|null
     */
    public function calculateNextDistribution(int $vehiculeId, int $userId): ?int
    {
        // Get the last distribution km for the vehicle
        $lastDistribution = $this->depenseRepository->findLastDistributionByVehiculeAndUser($vehiculeId, $userId);
        $lastDistributionKm = $lastDistribution?->getKmVehicule();

        //Get the number of km between vidanges for the vehicle
        $vehicule = $this->vehiculeRepository->find($vehiculeId);
        $kmBetweenDistributions = $vehicule?->getKmDistribution();

        // If there is no last distribution, use the initial km of the vehicle
        if ($lastDistributionKm === null) {
            $lastDistributionKm = (int) $this->vehiculeRepository->findInitialKmByVehicule($vehiculeId);
        }

        // Calculate the next distribution km
        return (int) ($lastDistributionKm + $kmBetweenDistributions);
    }

    /**
     * Calcul of next date to maintenance distribution for a vehicule
     * @param integer $vehiculeId
     * @param integer $userId
     * @return \DateTimeImmutable|null
     */
    public function calculateNextDistributionDate(int $vehiculeId, int $userId): ?\DateTimeImmutable
    {
        //Get the last distribution date for the vehicle
        $lastDistributionDate = $this->depenseRepository->findLastDistributionDateByVehiculeAndUser($vehiculeId, $userId);
        //Get the number of years between distributions for the vehicle
        $vehicule = $this->vehiculeRepository->find($vehiculeId);
        $yearsBetweenDistributions = $vehicule?->getAnneeDistribution();

        if ($lastDistributionDate === null) {
            return null;
        }

        $nextDistributionDate = \DateTimeImmutable::createFromInterface($lastDistributionDate)
            ->modify("+$yearsBetweenDistributions years");

        return $nextDistributionDate;
    }

    /**
     * Alert for next distribution for a vehicule
     *
     * @param integer $vehiculeId
     * @param integer $userId
     * @return bool
     */
    public function alertNextDistribution(int $vehiculeId, int $userId): bool
    {
        $nextDistributionKm = $this->calculateNextDistribution($vehiculeId, $userId);
        $currentKm = (int) $this->depenseRepository->findLastKmByVehicule($vehiculeId, $userId);
        $nextDistributionDate = $this->calculateNextDistributionDate($vehiculeId, $userId);
        $currentDate = new \DateTimeImmutable();
        return $currentKm >= $nextDistributionKm || ($nextDistributionDate !== null && $currentDate >= $nextDistributionDate);
    }
}