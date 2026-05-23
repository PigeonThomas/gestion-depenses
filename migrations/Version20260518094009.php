<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260518094009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('UPDATE categorie SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL OR updated_at IS NULL');
        $this->addSql('ALTER TABLE categorie MODIFY created_at DATETIME NOT NULL, MODIFY updated_at DATETIME NOT NULL');

        $this->addSql('ALTER TABLE depense ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('UPDATE depense SET updated_at = NOW() WHERE updated_at IS NULL');
        $this->addSql('ALTER TABLE depense MODIFY updated_at DATETIME NOT NULL');

        $this->addSql('ALTER TABLE magasin ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('UPDATE magasin SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL OR updated_at IS NULL');
        $this->addSql('ALTER TABLE magasin MODIFY created_at DATETIME NOT NULL, MODIFY updated_at DATETIME NOT NULL');

        $this->addSql('ALTER TABLE `user` ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD image_user VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE `user` SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL OR updated_at IS NULL');
        $this->addSql('ALTER TABLE `user` MODIFY created_at DATETIME NOT NULL, MODIFY updated_at DATETIME NOT NULL');

        $this->addSql('ALTER TABLE vehicule ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('UPDATE vehicule SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL OR updated_at IS NULL');
        $this->addSql('ALTER TABLE vehicule MODIFY created_at DATETIME NOT NULL, MODIFY updated_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE depense DROP updated_at');
        $this->addSql('ALTER TABLE magasin DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE user DROP created_at, DROP updated_at, DROP image_user');
        $this->addSql('ALTER TABLE vehicule DROP created_at, DROP updated_at');
    }
}
