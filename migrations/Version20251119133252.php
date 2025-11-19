<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251119133252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE marvel (code VARCHAR(255) NOT NULL, aliases JSONB DEFAULT NULL, authors JSONB DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, images JSONB DEFAULT NULL, main_color VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, partners JSONB DEFAULT NULL, powers JSONB DEFAULT NULL, ranking JSONB DEFAULT NULL, secret_identities JSONB DEFAULT NULL, species JSONB DEFAULT NULL, super_name VARCHAR(255) DEFAULT NULL, teams JSONB DEFAULT NULL, urls JSONB DEFAULT NULL, PRIMARY KEY (code))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE marvel');
    }
}
