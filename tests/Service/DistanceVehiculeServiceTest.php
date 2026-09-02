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

    // Test de la distance parcourue pour un véhicule avec un km enregistré pour le mois en cours et un km enregistré avant ce mois
    public function testCalculatesDistanceWhenBothMonthsHaveKm(): void
    {
        $this->depenseRepository->method('findLastKmByVehiculeAndMonth')->willReturn('20000'); // km de juin
        $this->depenseRepository->method('findLastKmByVehiculeBeforeMonth')->willReturn('18000'); // km de mai

        $result = $this->service->calculateDistance(1, 2024, 6, 42);

        $this->assertSame('2000', $result);
    }

    // Test de la distance parcourue pour un véhicule avec un km enregistré pour le mois en cours mais aucun km avant ce mois (doit utiliser le km d'achat)
    public function testUsesInitialKmWhenNoPreviousMonthRecord(): void
    {
        $this->depenseRepository->method('findLastKmByVehiculeAndMonth')->willReturn('55000'); // km de juin
        $this->depenseRepository->method('findLastKmByVehiculeBeforeMonth')->willReturn(null); // pas de km avant juin

        $this->vehiculeRepository->method('findInitialKmByVehicule')->willReturn('50000');

        $result = $this->service->calculateDistance(1, 2024, 6, 42);

        $this->assertSame('5000', $result);
    }

    // Test de la distance parcourue en janvier, avec le dernier km connu remontant à décembre de l'année précédente
    public function testHandlesJanuaryCorrectly(): void
    {
        $this->depenseRepository->method('findLastKmByVehiculeAndMonth')->willReturn('30000');
        $this->depenseRepository->method('findLastKmByVehiculeBeforeMonth')->willReturn('28000');

        $result = $this->service->calculateDistance(1, 2024, 1, 42);

        $this->assertSame('2000', $result);
    }

    // Test de la distance parcourue lorsque plusieurs mois se sont écoulés sans aucun relevé de km (ne doit pas utiliser le km d'achat tant qu'un relevé antérieur existe)
    public function testUsesLastKnownKmAcrossGapOfSeveralMonths(): void
    {
        $this->depenseRepository->method('findLastKmByVehiculeAndMonth')->willReturn('171038'); // km de juin
        $this->depenseRepository->method('findLastKmByVehiculeBeforeMonth')->willReturn('170736'); // dernier relevé connu, en janvier

        $result = $this->service->calculateDistance(1, 2026, 6, 42);

        $this->assertSame('302', $result);
    }

    // Test de la distance parcourue pour un véhicule avec un km enregistré pour le mois en cours et un km d'achat défini, mais pas de km pour le mois précédent (doit retourner 0 si le km du mois en cours est inférieur au km d'achat)
    public function testReturnsZeroWhenCurrentMonthKmIsEmptyString(): void
    {
        $this->depenseRepository->method('findLastKmByVehiculeAndMonth')->willReturn('');

        $result = $this->service->calculateDistance(1, 2024, 6, 42);

        $this->assertSame('0', $result);
    }
}
