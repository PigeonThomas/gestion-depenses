<?php

namespace App\Tests\Entity;

use App\Entity\Vehicule;
use PHPUnit\Framework\TestCase;

class VehiculeTest extends TestCase
{
    private Vehicule $vehicule;

    protected function setUp(): void
    {
        $this->vehicule = new Vehicule();
    }

    // Les tests suivants vérifient les getters et setters de l'entité Vehicule
    public function testIdIsNullByDefault(): void
    {
        $this->assertNull($this->vehicule->getId());
    }

    public function testSetAndGetSurnomVehicule(): void
    {
        $this->vehicule->setSurnomVehicule('Ma Clio');

        $this->assertSame('Ma Clio', $this->vehicule->getSurnomVehicule());
    }

    public function testSetAndGetImmatVehicule(): void
    {
        $this->vehicule->setImmatVehicule('AB-123-CD');

        $this->assertSame('AB-123-CD', $this->vehicule->getImmatVehicule());
    }

    public function testImmatVehiculeCanBeNull(): void
    {
        $this->vehicule->setImmatVehicule(null);

        $this->assertNull($this->vehicule->getImmatVehicule());
    }

    public function testSetAndGetTypeVehicule(): void
    {
        $this->vehicule->setTypeVehicule('voiture');

        $this->assertSame('voiture', $this->vehicule->getTypeVehicule());
    }

    public function testSetAndGetMarqueVehicule(): void
    {
        $this->vehicule->setMarqueVehicule('Renault');

        $this->assertSame('Renault', $this->vehicule->getMarqueVehicule());
    }

    public function testSetAndGetModeleVehicule(): void
    {
        $this->vehicule->setModeleVehicule('Clio 4');

        $this->assertSame('Clio 4', $this->vehicule->getModeleVehicule());
    }

    public function testSetAndGetEnergieVehicule(): void
    {
        $this->vehicule->setEnergieVehicule('Essence');

        $this->assertSame('Essence', $this->vehicule->getEnergieVehicule());
    }

    public function testSetAndGetKmAchatVehicule(): void
    {
        $this->vehicule->setKmAchatVehicule('50000.00');

        $this->assertSame('50000.00', $this->vehicule->getKmAchatVehicule());
    }

    public function testKmAchatVehiculeCanBeNull(): void
    {
        $this->vehicule->setKmAchatVehicule(null);

        $this->assertNull($this->vehicule->getKmAchatVehicule());
    }

    public function testSetAndGetAnneeCirculationVehicule(): void
    {
        $date = new \DateTimeImmutable('2020-01-01');
        $this->vehicule->setAnneeCirculationVehicule($date);

        $this->assertSame($date, $this->vehicule->getAnneeCirculationVehicule());
    }

    public function testDepensesCollectionIsEmptyByDefault(): void
    {
        $this->assertCount(0, $this->vehicule->getDepenses());
    }

    // Les tests suivants vérifient les constantes et méthodes utilitaires de l'entité Vehicule
    public function testTypeChoicesContainsExpectedValues(): void
    {
        $this->assertArrayHasKey('Voiture', Vehicule::TYPE_CHOICES);
        $this->assertArrayHasKey('Moto', Vehicule::TYPE_CHOICES);
        $this->assertSame('voiture', Vehicule::TYPE_CHOICES['Voiture']);
        $this->assertSame('moto', Vehicule::TYPE_CHOICES['Moto']);
    }

    // Test de la méthode setSurnomVehicule pour vérifier qu'elle retourne $this (fluent interface)
    public function testSetterReturnsFluent(): void
    {
        $result = $this->vehicule->setSurnomVehicule('Test');

        $this->assertSame($this->vehicule, $result);
    }
}
