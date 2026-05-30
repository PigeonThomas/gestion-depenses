<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260530150708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depense CHANGE commentaire_depense commentaire_depense TINYTEXT DEFAULT NULL, CHANGE repa_description repa_description TINYTEXT DEFAULT NULL, CHANGE repa_ref_pieces repa_ref_pieces TINYTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depense CHANGE commentaire_depense commentaire_depense VARCHAR(255) DEFAULT NULL, CHANGE repa_description repa_description VARCHAR(255) DEFAULT NULL, CHANGE repa_ref_pieces repa_ref_pieces VARCHAR(255) DEFAULT NULL');
    }
}
