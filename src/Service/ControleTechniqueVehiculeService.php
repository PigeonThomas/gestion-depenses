<?php

namespace App\Service;

use App\Repository\DepenseRepository;
use App\Repository\VehiculeRepository;

class ControleTechniqueVehiculeService
{
    private DepenseRepository $depenseRepository;
    private VehiculeRepository $vehiculeRepository;

    public function __construct(DepenseRepository $depenseRepository, VehiculeRepository $vehiculeRepository)
    {
        $this->depenseRepository = $depenseRepository;
        $this->vehiculeRepository = $vehiculeRepository;
    }

    /**
     * Calcul of next contrôle technique date for a vehicule.
     * Returns null when the vehicule has no periodicity set (non applicable) or no contrôle technique recorded yet.
     *
     * @param integer $vehiculeId
     * @param integer $userId
     * @return \DateTimeImmutable|null
     */
    public function calculateNextControleTechniqueDate(int $vehiculeId, int $userId): ?\DateTimeImmutable
    {
        $vehicule = $this->vehiculeRepository->find($vehiculeId);
        $anneesEntreControles = $vehicule?->getAnneeControleTechnique();

        if ($anneesEntreControles === null) {
            return null;
        }

        $lastControleTechniqueDate = $this->depenseRepository->findLastControleTechniqueDateByVehiculeAndUser($vehiculeId, $userId);

        // Fallback to the mise en circulation date when no contrôle technique has ever been recorded
        if ($lastControleTechniqueDate === null) {
            $lastControleTechniqueDate = $vehicule?->getAnneeCirculationVehicule();
        }

        if ($lastControleTechniqueDate === null) {
            return null;
        }

        return \DateTimeImmutable::createFromInterface($lastControleTechniqueDate)
            ->modify("+$anneesEntreControles years");
    }

    /**
     * Alert for next contrôle technique for a vehicule.
     * Returns false when the vehicule has no periodicity set (non applicable).
     *
     * @param integer $vehiculeId
     * @param integer $userId
     * @return bool
     */
    public function alertNextControleTechnique(int $vehiculeId, int $userId): bool
    {
        $vehicule = $this->vehiculeRepository->find($vehiculeId);
        if ($vehicule?->getAnneeControleTechnique() === null) {
            return false;
        }

        $nextControleTechniqueDate = $this->calculateNextControleTechniqueDate($vehiculeId, $userId);

        if ($nextControleTechniqueDate === null) {
            return false;
        }

        return (new \DateTimeImmutable()) >= $nextControleTechniqueDate;
    }
}
