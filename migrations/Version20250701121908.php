<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250701121908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE instrument (id VARCHAR(255) NOT NULL, name TEXT NOT NULL, description VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT NULL, tags JSONB DEFAULT NULL, genres JSONB DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE work (id UUID NOT NULL, title TEXT NOT NULL, type VARCHAR(255) DEFAULT NULL, tags JSONB DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE instrument');
        $this->addSql('DROP TABLE work');
    }
}
