<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240106132949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE processed_messages_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE processed_messages (id INT NOT NULL, run_id INT NOT NULL, attempt SMALLINT NOT NULL, message_type VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, dispatched_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, received_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, finished_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, memory_usage INT NOT NULL, transport VARCHAR(255) NOT NULL, tags VARCHAR(255) DEFAULT NULL, failure_type VARCHAR(255) DEFAULT NULL, failure_message TEXT DEFAULT NULL, results JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN processed_messages.dispatched_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN processed_messages.received_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN processed_messages.finished_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE official ADD wikidata_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE official ADD wiki_data JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE official ADD image_codes JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE official ADD image_count INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE processed_messages_id_seq CASCADE');
        $this->addSql('DROP TABLE processed_messages');
        $this->addSql('ALTER TABLE official DROP wikidata_id');
        $this->addSql('ALTER TABLE official DROP wiki_data');
        $this->addSql('ALTER TABLE official DROP image_codes');
        $this->addSql('ALTER TABLE official DROP image_count');
    }
}
