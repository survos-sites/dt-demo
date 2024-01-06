<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240106155319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE term DROP CONSTRAINT FK_A50FE78D4D88E615');
        $this->addSql('ALTER TABLE term ADD CONSTRAINT FK_A50FE78D4D88E615 FOREIGN KEY (official_id) REFERENCES official (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE term DROP CONSTRAINT fk_a50fe78d4d88e615');
        $this->addSql('ALTER TABLE term ADD CONSTRAINT fk_a50fe78d4d88e615 FOREIGN KEY (official_id) REFERENCES official (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
