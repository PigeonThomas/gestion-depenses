<?php

namespace App\Service;

use App\Repository\DepenseRepository;
use App\Repository\VehiculeRepository;

class VidangeVehiculeService
{
    private DepenseRepository $depenseRepository;
    private VehiculeRepository $vehiculeRepository;

    public function __construct(DepenseRepository $depenseRepository, VehiculeRepository $vehiculeRepository)
    {
        $this->depenseRepository = $depenseRepository;
        $this->vehiculeRepository = $vehiculeRepository;
    }

    /**
     * Calcul of next vidange for a vehicule
     *
     * @param integer $vehiculeId
     * @param integer $userId
     * @return int|null
     */
    public function calculateNextVidange(int $vehiculeId, int $userId): ?int
    {
        // Get the last vidange km for the vehicle
        $lastVidangeKm = $this->depenseRepository->findLastVidangeKmByVehicule($vehiculeId, $userId);

        //Get the number of km between vidanges for the vehicle
        $vehicule = $this->vehiculeRepository->find($vehiculeId);
        $kmBetweenVidanges = $vehicule?->getKmVidange();

        // If there is no last vidange, use the initial km of the vehicle
        if ($lastVidangeKm === null) {
            $lastVidangeKm = (int) $this->vehiculeRepository->findInitialKmByVehicule($vehiculeId);
        }

        // Calculate the next vidange km (every 10,000 km)
        return (int) ($lastVidangeKm + $kmBetweenVidanges);
    }

    /**
     * Alert for next vidange for a vehicule
     *
     * @param integer $vehiculeId
     * @param integer $userId
     * @return bool
     */
    public function alertNextVidange(int $vehiculeId, int $userId): bool
    {
        $nextVidangeKm = $this->calculateNextVidange($vehiculeId, $userId);
        $currentKm = (int) $this->depenseRepository->findLastKmByVehicule($vehiculeId, $userId);
        return $currentKm >= $nextVidangeKm;
    }
}