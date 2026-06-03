<?php

namespace App\Tests\Entity;

use App\Entity\Categorie;
use PHPUnit\Framework\TestCase;

class CategorieTest extends TestCase
{
    private Categorie $categorie;

    protected function setUp(): void
    {
        $this->categorie = new Categorie();
    }

    // Les tests suivants vérifient les getters et setters de l'entité Categorie
    public function testIdIsNullByDefault(): void
    {
        $this->assertNull($this->categorie->getId());
    }

    public function testSetAndGetNomCategorie(): void
    {
        $this->categorie->setNomCategorie('Carburant');

        $this->assertSame('Carburant', $this->categorie->getNomCategorie());
    }

    public function testSetAndGetCouleurCategorie(): void
    {
        $this->categorie->setCouleurCategorie('#FF5733');

        $this->assertSame('#FF5733', $this->categorie->getCouleurCategorie());
    }

    public function testSetAndGetIconeCategorie(): void
    {
        $this->categorie->setIconeCategorie('fa-solid fa-gas-pump');

        $this->assertSame('fa-solid fa-gas-pump', $this->categorie->getIconeCategorie());
    }

    public function testSetIconeCategorieExtractsClassFromHtml(): void
    {
        // Si on passe du HTML avec class="...", la méthode extrait juste la valeur de class
        $this->categorie->setIconeCategorie('<i class="fa-solid fa-car"></i>');

        $this->assertSame('fa-solid fa-car', $this->categorie->getIconeCategorie());
    }

    public function testIconeCategorieCanBeNull(): void
    {
        $this->categorie->setIconeCategorie(null);

        $this->assertNull($this->categorie->getIconeCategorie());
    }

    public function testGetIconeCategorieClassReturnsNullWhenIconeIsNull(): void
    {
        $this->categorie->setIconeCategorie(null);

        $this->assertNull($this->categorie->getIconeCategorieClass());
    }

    public function testGetIconeCategorieClassReturnsSameValueWhenNoHtml(): void
    {
        $this->categorie->setIconeCategorie('fa-solid fa-gas-pump');

        $this->assertSame('fa-solid fa-gas-pump', $this->categorie->getIconeCategorieClass());
    }

    public function testDepensesCollectionIsEmptyByDefault(): void
    {
        $this->assertCount(0, $this->categorie->getDepenses());
    }

    public function testSetAndGetCreatedAt(): void
    {
        $date = new \DateTimeImmutable('2024-01-01 10:00:00');
        $this->categorie->setCreatedAt($date);

        $this->assertSame($date, $this->categorie->getCreatedAt());
    }

    public function testSetAndGetUpdatedAt(): void
    {
        $date = new \DateTimeImmutable('2024-06-01 15:30:00');
        $this->categorie->setUpdatedAt($date);

        $this->assertSame($date, $this->categorie->getUpdatedAt());
    }

    public function testIconChoicesContainsExpectedKeys(): void
    {
        $this->assertArrayHasKey('Carburant', Categorie::ICON_CHOICES);
        $this->assertArrayHasKey('Reparation', Categorie::ICON_CHOICES);
        $this->assertSame('fa-solid fa-gas-pump', Categorie::ICON_CHOICES['Carburant']);
    }

    public function testSetterReturnsFluent(): void
    {
        $result = $this->categorie->setNomCategorie('Test');

        $this->assertSame($this->categorie, $result);
    }
}
