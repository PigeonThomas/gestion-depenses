<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260530131804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rend les categories uniques par nom et couleur, et force une couleur non nulle';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie CHANGE nom_categorie nom_categorie VARCHAR(50) NOT NULL, CHANGE couleur_categorie couleur_categorie VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_497DD634DD8CA775 ON categorie (nom_categorie)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_497DD63423D8066D ON categorie (couleur_categorie)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_497DD634DD8CA775 ON categorie');
        $this->addSql('DROP INDEX UNIQ_497DD63423D8066D ON categorie');
        $this->addSql('ALTER TABLE categorie CHANGE nom_categorie nom_categorie VARCHAR(255) NOT NULL, CHANGE couleur_categorie couleur_categorie VARCHAR(255) DEFAULT NULL');
    }
}
