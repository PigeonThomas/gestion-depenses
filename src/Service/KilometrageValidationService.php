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

        // Cherche le dernier km enregistré pour ce véhicule (toutes dates confondues)
        $lastKm = $this->depenseRepository->findLastKmByVehicule($vehicule->getId(), $depense->getId());

        if ($lastKm !== null) {
            if ($kmSaisi <= (float) $lastKm) {
                return sprintf(
                    'Le kilométrage saisi (%s km) doit être supérieur au dernier kilométrage enregistré (%s km) pour ce véhicule.',
                    number_format($kmSaisi, 0, ',', ' '),
                    number_format((float) $lastKm, 0, ',', ' ')
                );
            }

            return null;
        }

        // Aucun km en BDD : compare avec le km à l'achat du véhicule
        $kmAchat = $vehicule->getKmAchatVehicule();
        if ($kmAchat !== null && $kmSaisi <= (float) $kmAchat) {
            return sprintf(
                'Le kilométrage saisi (%s km) doit être supérieur au kilométrage à l\'achat du véhicule (%s km).',
                number_format($kmSaisi, 0, ',', ' '),
                number_format((float) $kmAchat, 0, ',', ' ')
            );
        }

        return null;
    }
}
