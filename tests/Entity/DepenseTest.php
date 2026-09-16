<?php

namespace App\Tests\Entity;

use App\Entity\Categorie;
use App\Entity\Depense;
use App\Entity\Magasin;
use App\Entity\User;
use App\Entity\Vehicule;
use PHPUnit\Framework\TestCase;

class DepenseTest extends TestCase
{
    private Depense $depense;

    protected function setUp(): void
    {
        $this->depense = new Depense();
    }

    // Les tests suivants vérifient les getters et setters de l'entité Depense
    public function testDefaultMontantIsNull(): void
    {
        $this->assertNull($this->depense->getMontantDepense());
    }

    public function testSetAndGetMontantDepense(): void
    {
        $this->depense->setMontantDepense('42.50');

        $this->assertSame('42.50', $this->depense->getMontantDepense());
    }

    public function testSetAndGetDateDepense(): void
    {
        $date = new \DateTimeImmutable('2024-03-15');
        $this->depense->setDateDepense($date);

        $this->assertSame($date, $this->depense->getDateDepense());
    }

    public function testSetAndGetCommentaireDepense(): void
    {
        $this->depense->setCommentaireDepense('Plein de carburant');

        $this->assertSame('Plein de carburant', $this->depense->getCommentaireDepense());
    }

    public function testCommentaireDepenseCanBeNull(): void
    {
        $this->depense->setCommentaireDepense(null);

        $this->assertNull($this->depense->getCommentaireDepense());
    }

    public function testSetAndGetKmVehicule(): void
    {
        $this->depense->setKmVehicule('15000.00');

        $this->assertSame('15000.00', $this->depense->getKmVehicule());
    }

    public function testKmVehiculeCanBeNull(): void
    {
        $this->depense->setKmVehicule(null);

        $this->assertNull($this->depense->getKmVehicule());
    }

    public function testSetAndGetCarbuPrixLitre(): void
    {
        $this->depense->setCarbuPrixLitre('1.850');

        $this->assertSame('1.850', $this->depense->getCarbuPrixLitre());
    }

    public function testSetAndGetRepaDescription(): void
    {
        $this->depense->setRepaDescription('Changement de plaquettes');

        $this->assertSame('Changement de plaquettes', $this->depense->getRepaDescription());
    }

    public function testSetAndGetUser(): void
    {
        $user = new User();
        $user->setEmail('john@example.com');
        $this->depense->setUser($user);

        $this->assertSame($user, $this->depense->getUser());
    }

    // Test de la méthode setUser pour vérifier qu'elle retourne $this (fluent interface)
    public function testSetAndGetCategorie(): void
    {
        $categorie = new Categorie();
        $categorie->setNomCategorie('Carburant');
        $this->depense->setCategorie($categorie);

        $this->assertSame($categorie, $this->depense->getCategorie());
    }

    // Test de la méthode setCategorie pour vérifier qu'elle retourne $this (fluent interface)
    public function testSetCategorieToNull(): void
    {
        $this->depense->setCategorie(null);

        $this->assertNull($this->depense->getCategorie());
    }

    // Test de la méthode setMagasin pour vérifier qu'elle retourne $this (fluent interface)
    public function testSetAndGetMagasin(): void
    {
        $magasin = new Magasin();
        $magasin->setNomMagasin('Total');
        $this->depense->setMagasin($magasin);

        $this->assertSame($magasin, $this->depense->getMagasin());
    }

    // Test de la méthode setVehicule pour vérifier qu'elle retourne $this (fluent interface)
    public function testSetAndGetVehicule(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setSurnomVehicule('Ma voiture');
        $this->depense->setVehicule($vehicule);

        $this->assertSame($vehicule, $this->depense->getVehicule());
    }

    public function testIdIsNullByDefault(): void
    {
        $this->assertNull($this->depense->getId());
    }

    // Test de la méthode setMontantDepense pour vérifier qu'elle retourne $this (fluent interface)
    public function testSetterReturnsFluent(): void
    {
        $result = $this->depense->setMontantDepense('10.00');

        $this->assertSame($this->depense, $result);
    }
}
