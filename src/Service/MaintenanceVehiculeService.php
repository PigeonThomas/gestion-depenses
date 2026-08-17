<?php

namespace App\Service;

use App\Repository\VehiculeRepository;

class MaintenanceVehiculeService
{
    public function __construct(
        private readonly VehiculeRepository $vehiculeRepository,
        private readonly VidangeVehiculeService $vidangeVehiculeService,
        private readonly DistributionVehiculeService $distributionVehiculeService,
    ) {
    }

    /**
     * Retourne l'état des alertes de maintenance pour les véhicules d'un utilisateur.
     *
     * @return array<int, array{vidange: bool, distribution: bool}>
     */
    public function getAlerts(int $userId): array
    {
        $alerts = [];

        foreach ($this->vehiculeRepository->findByUserId($userId) as $vehicule) {
            $vehiculeId = $vehicule->getId();

            if ($vehiculeId === null) {
                continue;
            }

            $alerts[$vehiculeId] = [
                'vidange' => $this->vidangeVehiculeService->alertNextVidange($vehiculeId, $userId),
                'distribution' => $this->distributionVehiculeService->alertNextDistribution($vehiculeId, $userId),
            ];
        }

        return $alerts;
    }

    /**
     * Retourne le nombre total d'alertes de maintenance pour un utilisateur.
     */
    public function countAlerts(int $userId): int
    {
        $count = 0;

        foreach ($this->getAlerts($userId) as $alert) {
            $count += (int) $alert['vidange'];
            $count += (int) $alert['distribution'];
        }

        return $count;
    }
}