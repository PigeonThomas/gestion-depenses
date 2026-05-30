<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260530153629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_identifier_email TO UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_292FFF1D830C1CA1 ON vehicule (immat_vehicule)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_292FFF1DB3265B2E ON vehicule (surnom_vehicule)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_8d93d649e7927c74 TO UNIQ_IDENTIFIER_EMAIL');
        $this->addSql('DROP INDEX UNIQ_292FFF1D830C1CA1 ON vehicule');
        $this->addSql('DROP INDEX UNIQ_292FFF1DB3265B2E ON vehicule');
    }
}
