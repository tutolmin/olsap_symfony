<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220703125101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE environments ADD status_id INT NOT NULL');
        $this->addSql('ALTER TABLE environments ADD CONSTRAINT FK_CE28A8316BF700BD FOREIGN KEY (status_id) REFERENCES environment_statuses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_CE28A8316BF700BD ON environments (status_id)');
        $this->addSql('ALTER TABLE instances ADD status_id INT NOT NULL');
        $this->addSql('ALTER TABLE instances DROP staus');
        $this->addSql('ALTER TABLE instances ADD CONSTRAINT FK_7A2700696BF700BD FOREIGN KEY (status_id) REFERENCES instance_statuses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7A2700696BF700BD ON instances (status_id)');
        $this->addSql('ALTER TABLE sessions ADD status_id INT NOT NULL');
        $this->addSql('ALTER TABLE sessions DROP status');
        $this->addSql('ALTER TABLE sessions ADD CONSTRAINT FK_9A609D136BF700BD FOREIGN KEY (status_id) REFERENCES session_statuses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9A609D136BF700BD ON sessions (status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE instances DROP CONSTRAINT FK_7A2700696BF700BD');
        $this->addSql('DROP INDEX IDX_7A2700696BF700BD');
        $this->addSql('ALTER TABLE instances ADD staus TEXT NOT NULL');
        $this->addSql('ALTER TABLE instances DROP status_id');
        $this->addSql('COMMENT ON COLUMN instances.staus IS \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE environments DROP CONSTRAINT FK_CE28A8316BF700BD');
        $this->addSql('DROP INDEX IDX_CE28A8316BF700BD');
        $this->addSql('ALTER TABLE environments DROP status_id');
        $this->addSql('ALTER TABLE sessions DROP CONSTRAINT FK_9A609D136BF700BD');
        $this->addSql('DROP INDEX IDX_9A609D136BF700BD');
        $this->addSql('ALTER TABLE sessions ADD status TEXT NOT NULL');
        $this->addSql('ALTER TABLE sessions DROP status_id');
        $this->addSql('COMMENT ON COLUMN sessions.status IS \'(DC2Type:simple_array)\'');
    }
}
