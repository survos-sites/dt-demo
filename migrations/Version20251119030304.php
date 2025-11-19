<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251119030304 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE wam (title VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, wam_image TEXT DEFAULT NULL, accession_number VARCHAR(255) DEFAULT NULL, cataloguer VARCHAR(255) DEFAULT NULL, collection VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, keywords JSONB DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, notes TEXT DEFAULT NULL, registration_number VARCHAR(255) NOT NULL, subject JSONB DEFAULT NULL, condition VARCHAR(255) DEFAULT NULL, creator VARCHAR(255) DEFAULT NULL, digital_id VARCHAR(255) DEFAULT NULL, medium VARCHAR(255) DEFAULT NULL, medium_format VARCHAR(255) DEFAULT NULL, notes2 TEXT DEFAULT NULL, original_legal_broker VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, PRIMARY KEY (registration_number))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE wam');
    }
}
