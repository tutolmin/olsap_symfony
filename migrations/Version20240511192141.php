<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240511192141 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hardware_profiles ALTER cost SET DEFAULT 100');
        $this->addSql('CREATE INDEX hardware_profiles_supported ON hardware_profiles (supported)');
        $this->addSql('CREATE INDEX hardware_profiles_cost ON hardware_profiles (cost)');
        $this->addSql('CREATE INDEX hardware_profiles_type ON hardware_profiles (type)');
        $this->addSql('ALTER TABLE messenger_messages ALTER id TYPE INT');
        $this->addSql('ALTER TABLE messenger_messages ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE messenger_messages ALTER queue_name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE messenger_messages ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE messenger_messages ALTER available_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE messenger_messages ALTER delivered_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER INDEX idx_75ea56e0fb7336f0 RENAME TO queue_name');
        $this->addSql('ALTER INDEX idx_75ea56e0e3bd61ce RENAME TO available_at');
        $this->addSql('ALTER INDEX idx_75ea56e016ba31db RENAME TO delivered_at');
        $this->addSql('CREATE INDEX operating_systems_supported ON operating_systems (supported)');
        $this->addSql('ALTER INDEX idx_810d851aa8b4a30f RENAME TO operating_systems_breed');
        $this->addSql('ALTER INDEX idx_4ccbfb18115f0ee5 RENAME TO technologies_domain');
        $this->addSql('ALTER TABLE testees ALTER registered_at SET DEFAULT CURRENT_TIMESTAMP');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX operating_systems_supported');
        $this->addSql('ALTER INDEX operating_systems_breed RENAME TO idx_810d851aa8b4a30f');
        $this->addSql('ALTER INDEX technologies_domain RENAME TO idx_4ccbfb18115f0ee5');
        $this->addSql('DROP INDEX hardware_profiles_supported');
        $this->addSql('DROP INDEX hardware_profiles_cost');
        $this->addSql('DROP INDEX hardware_profiles_type');
        $this->addSql('ALTER TABLE hardware_profiles ALTER cost DROP DEFAULT');
        $this->addSql('ALTER TABLE testees ALTER registered_at SET DEFAULT \'CURRENT_DATE\'');
        $this->addSql('ALTER TABLE messenger_messages ALTER id TYPE BIGINT');
        $this->addSql('CREATE SEQUENCE messenger_messages_id_seq');
        $this->addSql('SELECT setval(\'messenger_messages_id_seq\', (SELECT MAX(id) FROM messenger_messages))');
        $this->addSql('ALTER TABLE messenger_messages ALTER id SET DEFAULT nextval(\'messenger_messages_id_seq\')');
        $this->addSql('ALTER TABLE messenger_messages ALTER queue_name TYPE VARCHAR(190)');
        $this->addSql('ALTER TABLE messenger_messages ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE messenger_messages ALTER available_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE messenger_messages ALTER delivered_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS NULL');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS NULL');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS NULL');
        $this->addSql('ALTER INDEX delivered_at RENAME TO idx_75ea56e016ba31db');
        $this->addSql('ALTER INDEX available_at RENAME TO idx_75ea56e0e3bd61ce');
        $this->addSql('ALTER INDEX queue_name RENAME TO idx_75ea56e0fb7336f0');
    }
}
