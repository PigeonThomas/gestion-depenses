<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260816085748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depense ADD repa_vidange TINYINT DEFAULT NULL, ADD repa_distribution TINYINT DEFAULT NULL');
        $this->addSql('ALTER TABLE vehicule ADD km_vidange NUMERIC(5, 0) DEFAULT NULL, ADD km_distribution NUMERIC(5, 0) DEFAULT NULL, ADD annee_distribution NUMERIC(1, 0) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depense DROP repa_vidange, DROP repa_distribution');
        $this->addSql('ALTER TABLE vehicule DROP km_vidange, DROP km_distribution, DROP annee_distribution');
    }
}
