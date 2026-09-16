<?php

namespace App\Service;

use App\Entity\Depense;
use App\Repository\DepenseRepository;

class KilometrageValidationService
{
    private const CATEGORIE_CARBURANT_ID = 10;
    private const CATEGORIE_REPARATION_ID = 2;

    public function __construct(private readonly DepenseRepository $depenseRepository)
    {
    }

    /**
     * Vérifie que le kilométrage saisi est cohérent avec l'historique du véhicule.
     * Retourne un message d'erreur si le km est invalide, null sinon.
     */
    public function validate(Depense $depense): ?string
    {
        // Pas de vérification si pas de véhicule associé
        $vehicule = $depense->getVehicule();
        if ($vehicule === null) {
            return null;
        }

        // Pas de vérification si la catégorie n'est pas carburant ou réparation
        $categorie = $depense->getCategorie();
        if ($categorie === null) {
            return null;
        }
        if (!in_array($categorie->getId(), [self::CATEGORIE_CARBURANT_ID, self::CATEGORIE_REPARATION_ID], true)) {
            return null;
        }

        $kmSaisi = (float) $depense->getKmVehicule();
        $dateDepense = $depense->getDateDepense();

        // Cherche le km de la dépense précédente (même véhicule, même catégorie, date antérieure ou égale)
        $previousKm = $this->depenseRepository->findPreviousKmByVehiculeAndCategorie(
            $vehicule->getId(),
            $categorie->getId(),
            $dateDepense,
            $depense->getId()
        );

        if ($previousKm !== null) {
            if ($kmSaisi <= (float) $previousKm) {
                return sprintf(
                    'Le kilométrage saisi (%s km) doit être supérieur au kilométrage de la dépense précédente (%s km) pour cette catégorie.',
                    number_format($kmSaisi, 0, ',', ' '),
                    number_format((float) $previousKm, 0, ',', ' ')
                );
            }
        } else {
            // Aucune dépense antérieure de cette catégorie : compare avec le km à l'achat du véhicule
            $kmAchat = $vehicule->getKmAchatVehicule();
            if ($kmAchat !== null && $kmSaisi <= (float) $kmAchat) {
                return sprintf(
                    'Le kilométrage saisi (%s km) doit être supérieur au kilométrage à l\'achat du véhicule (%s km).',
                    number_format($kmSaisi, 0, ',', ' '),
                    number_format((float) $kmAchat, 0, ',', ' ')
                );
            }
        }

        // Cherche le km de la dépense suivante (même véhicule, même catégorie, date postérieure ou égale)
        $nextKm = $this->depenseRepository->findNextKmByVehiculeAndCategorie(
            $vehicule->getId(),
            $categorie->getId(),
            $dateDepense,
            $depense->getId()
        );

        if ($nextKm !== null && $kmSaisi >= (float) $nextKm) {
            return sprintf(
                'Le kilométrage saisi (%s km) doit être inférieur au kilométrage de la dépense suivante (%s km) pour cette catégorie.',
                number_format($kmSaisi, 0, ',', ' '),
                number_format((float) $nextKm, 0, ',', ' ')
            );
        }

        return null;
    }
}
