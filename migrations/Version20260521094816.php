<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260521094816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE magasin ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE magasin ADD CONSTRAINT FK_54AF5F27A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_54AF5F27A76ED395 ON magasin (user_id)');
        $this->addSql('ALTER TABLE vehicule ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_292FFF1DA76ED395 ON vehicule (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE magasin DROP FOREIGN KEY FK_54AF5F27A76ED395');
        $this->addSql('DROP INDEX IDX_54AF5F27A76ED395 ON magasin');
        $this->addSql('ALTER TABLE magasin DROP user_id');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1DA76ED395');
        $this->addSql('DROP INDEX IDX_292FFF1DA76ED395 ON vehicule');
        $this->addSql('ALTER TABLE vehicule DROP user_id');
    }
}
