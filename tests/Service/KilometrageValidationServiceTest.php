<?php

namespace App\Tests\Service;

use App\Entity\Categorie;
use App\Entity\Depense;
use App\Entity\Vehicule;
use App\Repository\DepenseRepository;
use App\Service\KilometrageValidationService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class KilometrageValidationServiceTest extends TestCase
{
    private DepenseRepository&MockObject $depenseRepository;
    private KilometrageValidationService $service;

    protected function setUp(): void
    {
        $this->depenseRepository = $this->createMock(DepenseRepository::class);
        $this->service = new KilometrageValidationService($this->depenseRepository);
    }

    private function makeDepense(int $categorieId, ?string $km, ?string $kmAchat = null): Depense
    {
        $vehicule = $this->createMock(Vehicule::class);
        $vehicule->method('getId')->willReturn(1);
        $vehicule->method('getKmAchatVehicule')->willReturn($kmAchat);

        $categorie = $this->createMock(Categorie::class);
        $categorie->method('getId')->willReturn($categorieId);

        $depense = new Depense();
        $depense->setVehicule($vehicule);
        $depense->setCategorie($categorie);
        $depense->setKmVehicule($km);
        $depense->setMontantDepense('50.00');
        $depense->setDateDepense(new \DateTimeImmutable());

        return $depense;
    }

    // Les tests suivants vérifient la logique de validation du kilométrage dans le service KilometrageValidationService
    // Test de la validation du kilométrage pour une dépense sans véhicule (doit retourner null)
    public function testReturnsNullWhenNoVehicule(): void
    {
        $depense = new Depense();
        $depense->setMontantDepense('50.00');
        $depense->setDateDepense(new \DateTimeImmutable());

        $this->depenseRepository->expects($this->never())->method('findLastKmByVehicule');

        $result = $this->service->validate($depense);

        $this->assertNull($result);
    }

    // Test de la validation du kilométrage pour une dépense sans catégorie (doit retourner null)
    public function testReturnsNullWhenNoCategorie(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setSurnomVehicule('Test');

        $depense = new Depense();
        $depense->setVehicule($vehicule);
        $depense->setMontantDepense('50.00');
        $depense->setDateDepense(new \DateTimeImmutable());

        $this->depenseRepository->expects($this->never())->method('findLastKmByVehicule');

        $result = $this->service->validate($depense);

        $this->assertNull($result);
    }

    // Test de la validation du kilométrage pour une dépense avec une catégorie qui n'est ni carburant ni réparation (doit retourner null)
    public function testReturnsNullForNonKmCategory(): void
    {
        // ID de catégorie qui n'est ni carburant (10) ni réparation (2)
        $depense = $this->makeDepense(5, '15000');

        $this->depenseRepository->expects($this->never())->method('findLastKmByVehicule');

        $result = $this->service->validate($depense);

        $this->assertNull($result);
    }

    // Test de la validation du kilométrage pour une dépense de carburant avec un km supérieur au dernier km enregistré (doit retourner null)
    public function testReturnsNullWhenKmIsHigherThanLastKm(): void
    {
        $depense = $this->makeDepense(10, '20000'); // carburant

        $this->depenseRepository->method('findLastKmByVehicule')->willReturn('15000');

        $result = $this->service->validate($depense);

        $this->assertNull($result);
    }

    // Test de la validation du kilométrage pour une dépense de carburant avec un km inférieur au dernier km enregistré (doit retourner un message d'erreur)
    public function testReturnsErrorWhenKmIsLowerThanLastKm(): void
    {
        $depense = $this->makeDepense(10, '10000'); // carburant

        $this->depenseRepository->method('findLastKmByVehicule')->willReturn('15000');

        $result = $this->service->validate($depense);

        $this->assertNotNull($result);
        $this->assertStringContainsString('10 000', $result);
        $this->assertStringContainsString('15 000', $result);
    }

    // Test de la validation du kilométrage pour une dépense de carburant avec un km égal au dernier km enregistré (doit retourner un message d'erreur)
    public function testReturnsErrorWhenKmEqualsLastKm(): void
    {
        $depense = $this->makeDepense(10, '15000'); // carburant

        $this->depenseRepository->method('findLastKmByVehicule')->willReturn('15000');

        $result = $this->service->validate($depense);

        $this->assertNotNull($result);
    }

    // Test de la validation du kilométrage pour une dépense de carburant sans km précédent mais avec un km d'achat défini (doit retourner null si km >= km achat)
    public function testReturnsNullWhenNoLastKmAndKmAboveAchat(): void
    {
        $depense = $this->makeDepense(10, '60000', '50000'); // km > km achat

        $this->depenseRepository->method('findLastKmByVehicule')->willReturn(null);

        $result = $this->service->validate($depense);

        $this->assertNull($result);
    }

    // Test de la validation du kilométrage pour une dépense de carburant sans km précédent mais avec un km d'achat défini (doit retourner un message d'erreur si km < km achat)
    public function testReturnsErrorWhenNoLastKmAndKmBelowAchat(): void
    {
        $depense = $this->makeDepense(10, '30000', '50000'); // km < km achat

        $this->depenseRepository->method('findLastKmByVehicule')->willReturn(null);

        $result = $this->service->validate($depense);

        $this->assertNotNull($result);
        $this->assertStringContainsString('achat', $result);
    }

    // Test de la validation du kilométrage pour une dépense de réparation avec un km supérieur au dernier km enregistré (doit retourner null)
    public function testWorksForReparationCategory(): void
    {
        $depense = $this->makeDepense(2, '20000'); // réparation

        $this->depenseRepository->method('findLastKmByVehicule')->willReturn('15000');

        $result = $this->service->validate($depense);

        $this->assertNull($result);
    }
}
