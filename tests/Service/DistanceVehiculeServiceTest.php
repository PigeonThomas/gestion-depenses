<?php

namespace App\Tests\Service;

use App\Repository\DepenseRepository;
use App\Repository\VehiculeRepository;
use App\Service\DistanceVehiculeService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DistanceVehiculeServiceTest extends TestCase
{
    private DepenseRepository&MockObject $depenseRepository;
    private VehiculeRepository&MockObject $vehiculeRepository;
    private DistanceVehiculeService $service;

    protected function setUp(): void
    {
        $this->depenseRepository = $this->createMock(DepenseRepository::class);
        $this->vehiculeRepository = $this->createMock(VehiculeRepository::class);
        $this->service = new DistanceVehiculeService($this->depenseRepository, $this->vehiculeRepository);
    }

    // Les tests suivants vérifient la logique de calcul de la distance parcourue dans le service DistanceVehiculeService
    // Test de la distance parcourue pour un véhicule sans km enregistré pour le mois en cours (doit retourner 0)
    public function testReturnsZeroWhenNoKmForCurrentMonth(): void
    {
        $this->depenseRepository->method('findLastKmByVehiculeAndMonth')->willReturn(null);

        $result = $this->service->calculateDistance(1, 2024, 6, 42);

        $this->assertSame('0', $result);
    }

    // Test de la distance parcourue pour un véhicule avec un km enregistré pour le mois en cours mais pas pour le mois précédent (doit utiliser le km d'achat)
    public function testCalculatesDistanceWhenBothMonthsHaveKm(): void
    {
        $this->depenseRepository->method('findLastKmByVehiculeAndMonth')
            ->willReturnCallback(function (int $vehiculeId, int $annee, int $mois): ?string {
                if ($mois === 6) {
                    return '20000'; // km de juin
                }
                return '18000'; // km de mai
            });

        $result = $this->service->calculateDistance(1, 2024, 6, 42);

        $this->assertSame('2000', $result);
    }

    // Test de la distance parcourue pour un véhicule avec un km enregistré pour le mois en cours mais pas pour le mois précédent (doit utiliser le km d'achat)
    public function testUsesInitialKmWhenNoPreviousMonthRecord(): void
    {
        $this->depenseRepository->method('findLastKmByVehiculeAndMonth')
            ->willReturnCallback(function (int $vehiculeId, int $annee, int $mois): ?string {
                if ($mois === 6) {
                    return '55000'; // km de juin
                }
                return null; // pas de km en mai
            });

        $this->vehiculeRepository->method('findInitialKmByVehicule')->willReturn('50000');

        $result = $this->service->calculateDistance(1, 2024, 6, 42);

        $this->assertSame('5000', $result);
    }

    // Test de la distance parcourue pour un véhicule avec un km enregistré pour le mois en cours et un km d'achat défini, mais pas de km pour le mois précédent (doit utiliser le km d'achat)
    public function testHandlesJanuaryCorrectly(): void
    {
        // En janvier, le mois précédent est décembre de l'année précédente
        $this->depenseRepository->method('findLastKmByVehiculeAndMonth')
            ->willReturnCallback(function (int $vehiculeId, int $annee, int $mois): ?string {
                if ($annee === 2024 && $mois === 1) {
                    return '30000';
                }
                if ($annee === 2023 && $mois === 12) {
                    return '28000';
                }
                return null;
            });

        $result = $this->service->calculateDistance(1, 2024, 1, 42);

        $this->assertSame('2000', $result);
    }

    // Test de la distance parcourue pour un véhicule avec un km enregistré pour le mois en cours et un km d'achat défini, mais pas de km pour le mois précédent (doit retourner 0 si le km du mois en cours est inférieur au km d'achat)
    public function testReturnsZeroWhenCurrentMonthKmIsEmptyString(): void
    {
        $this->depenseRepository->method('findLastKmByVehiculeAndMonth')->willReturn('');

        $result = $this->service->calculateDistance(1, 2024, 6, 42);

        $this->assertSame('0', $result);
    }
}
