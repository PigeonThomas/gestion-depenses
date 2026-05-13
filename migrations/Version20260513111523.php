<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260513111523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom_categorie VARCHAR(255) NOT NULL, couleur_categorie VARCHAR(255) DEFAULT NULL, icone_categorie VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE depense (id INT AUTO_INCREMENT NOT NULL, date_depense DATE NOT NULL, montant_depense NUMERIC(9, 2) NOT NULL, commentaire_depense VARCHAR(255) DEFAULT NULL, facture_depense VARCHAR(255) DEFAULT NULL, km_vehicule NUMERIC(8, 2) DEFAULT NULL, carbu_prix_litre NUMERIC(5, 3) DEFAULT NULL, repa_description VARCHAR(255) DEFAULT NULL, repa_ref_pieces VARCHAR(255) DEFAULT NULL, repa_photos VARCHAR(255) DEFAULT NULL, categorie_id INT DEFAULT NULL, magasin_id INT DEFAULT NULL, vehicule_id INT DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_34059757BCF5E72D (categorie_id), INDEX IDX_3405975720096AE3 (magasin_id), INDEX IDX_340597574A4A3511 (vehicule_id), INDEX IDX_34059757A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE magasin (id INT AUTO_INCREMENT NOT NULL, nom_magasin VARCHAR(255) NOT NULL, online_magasin TINYINT NOT NULL, adresse_magasin VARCHAR(255) DEFAULT NULL, lien_magasin VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom_user VARCHAR(255) NOT NULL, prenom_user VARCHAR(255) DEFAULT NULL, adresse_user VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE vehicule (id INT AUTO_INCREMENT NOT NULL, immat_vehicule VARCHAR(255) DEFAULT NULL, surnom_vehicule VARCHAR(255) NOT NULL, type_vehicule VARCHAR(255) NOT NULL, marque_vehicule VARCHAR(255) DEFAULT NULL, modele_vehicule VARCHAR(255) DEFAULT NULL, annee_circulation_vehicule DATE DEFAULT NULL, energie_vehicule VARCHAR(255) DEFAULT NULL, km_achat_vehicule NUMERIC(7, 0) DEFAULT NULL, image_vehicule VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE depense ADD CONSTRAINT FK_34059757BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE depense ADD CONSTRAINT FK_3405975720096AE3 FOREIGN KEY (magasin_id) REFERENCES magasin (id)');
        $this->addSql('ALTER TABLE depense ADD CONSTRAINT FK_340597574A4A3511 FOREIGN KEY (vehicule_id) REFERENCES vehicule (id)');
        $this->addSql('ALTER TABLE depense ADD CONSTRAINT FK_34059757A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depense DROP FOREIGN KEY FK_34059757BCF5E72D');
        $this->addSql('ALTER TABLE depense DROP FOREIGN KEY FK_3405975720096AE3');
        $this->addSql('ALTER TABLE depense DROP FOREIGN KEY FK_340597574A4A3511');
        $this->addSql('ALTER TABLE depense DROP FOREIGN KEY FK_34059757A76ED395');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE depense');
        $this->addSql('DROP TABLE magasin');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vehicule');
    }
}
