<?php

namespace App\Tests\Service;

use App\Entity\Vehicule;
use App\Repository\VehiculeRepository;
use App\Service\DistributionVehiculeService;
use App\Service\MaintenanceVehiculeService;
use App\Service\VidangeVehiculeService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MaintenanceVehiculeServiceTest extends TestCase
{
    private VehiculeRepository&MockObject $vehiculeRepository;
    private VidangeVehiculeService&MockObject $vidangeVehiculeService;
    private DistributionVehiculeService&MockObject $distributionVehiculeService;
    private MaintenanceVehiculeService $service;

    protected function setUp(): void
    {
        $this->vehiculeRepository = $this->createMock(VehiculeRepository::class);
        $this->vidangeVehiculeService = $this->createMock(VidangeVehiculeService::class);
        $this->distributionVehiculeService = $this->createMock(DistributionVehiculeService::class);
        $this->service = new MaintenanceVehiculeService(
            $this->vehiculeRepository,
            $this->vidangeVehiculeService,
            $this->distributionVehiculeService,
        );
    }

    public function testReturnsAlertsByVehicule(): void
    {
        $vehicule = $this->createMock(Vehicule::class);
        $vehicule->method('getId')->willReturn(7);

        $this->vehiculeRepository->method('findByUserId')->with(42)->willReturn([$vehicule]);
        $this->vidangeVehiculeService->method('alertNextVidange')->with(7, 42)->willReturn(true);
        $this->distributionVehiculeService->method('alertNextDistribution')->with(7, 42)->willReturn(false);

        $this->assertSame(
            [7 => ['vidange' => true, 'distribution' => false]],
            $this->service->getAlerts(42),
        );
    }

    public function testCountsBothMaintenanceAlerts(): void
    {
        $vehicule = $this->createMock(Vehicule::class);
        $vehicule->method('getId')->willReturn(7);

        $this->vehiculeRepository->method('findByUserId')->willReturn([$vehicule]);
        $this->vidangeVehiculeService->method('alertNextVidange')->willReturn(true);
        $this->distributionVehiculeService->method('alertNextDistribution')->willReturn(true);

        $this->assertSame(2, $this->service->countAlerts(42));
    }
}