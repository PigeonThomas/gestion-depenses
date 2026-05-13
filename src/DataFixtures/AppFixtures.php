<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Depense;
use App\Entity\Vehicule;
use App\Entity\Magasin;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // Création d'un utilisateur
        $user = new User();
        $user->setNomUser('admin');
        $user->setPrenomUser('admin');
        $user->setPassword('admin'); // Note: In a real application, passwords should be hashed
        $user->setEmail('admin@example.com');
        $manager->persist($user);

        //création d'une catégorie
        $categorie = new Categorie();
        $categorie->setNomCategorie('Alimentation');
        $categorie->setCouleurCategorie('#FF5733');
        $categorie->setIconeCategorie('fa-solid fa-utensils');
        $manager->persist($categorie);

        //création d'un magasin
        $magasin = new Magasin();
        $magasin->setNomMagasin('Carrefour');
        $magasin->setAdresseMagasin('123 Rue de la Paix, Paris');
        $magasin->setOnlineMagasin(false);
        $manager->persist($magasin);

        //création d'un véhicule
        $vehicule = new Vehicule();
        $vehicule->setImmatVehicule('AB-123-CD');
        $vehicule->setSurnomVehicule('Grobert');
        $vehicule->setTypeVehicule('Van');
        $vehicule->setMarqueVehicule('Fiat');
        $vehicule->setModeleVehicule('Ducato');
        $vehicule->setAnneeCirculationVehicule(new \DateTime('2015-01-01'));
        $manager->persist($vehicule);

        //Création dépense 1
        $depense1 = new Depense();
        $depense1->setMontantDepense(50.00);
        $depense1->setDateDepense(new \DateTime('2024-01-01'));
        $depense1->setCommentaireDepense('Achat de nourriture');
        $depense1->setCategorie($categorie);
        $depense1->setMagasin($magasin);
        $depense1->setVehicule($vehicule);
        $depense1->setUser($user);
        $manager->persist($depense1);

        //Création dépense 2
        $depense2 = new Depense();
        $depense2->setMontantDepense(30.00);
        $depense2->setDateDepense(new \DateTime('2024-02-01'));
        $depense2->setCommentaireDepense('Achat de carburant');
        $depense2->setCategorie($categorie);
        $depense2->setMagasin($magasin);
        $depense2->setVehicule($vehicule);
        $depense2->setUser($user);
        $manager->persist($depense2);

        $manager->flush();
    }
}
