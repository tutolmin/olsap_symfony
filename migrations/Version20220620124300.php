<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220620124300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE environments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_instance_types_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE environments (id INT NOT NULL, task_id INT NOT NULL, session_id INT DEFAULT NULL, instance_id INT DEFAULT NULL, status TEXT NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, valid BOOLEAN DEFAULT NULL, path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CE28A8318DB60186 ON environments (task_id)');
        $this->addSql('CREATE INDEX IDX_CE28A831613FECDF ON environments (session_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CE28A8313A51721D ON environments (instance_id)');
        $this->addSql('COMMENT ON COLUMN environments.status IS \'(DC2Type:simple_array)\'');
        $this->addSql('COMMENT ON COLUMN environments.started_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE task_instance_types (id INT NOT NULL, task_id INT NOT NULL, instance_type_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4987EEF78DB60186 ON task_instance_types (task_id)');
        $this->addSql('CREATE INDEX IDX_4987EEF72D84150 ON task_instance_types (instance_type_id)');
        $this->addSql('ALTER TABLE environments ADD CONSTRAINT FK_CE28A8318DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE environments ADD CONSTRAINT FK_CE28A831613FECDF FOREIGN KEY (session_id) REFERENCES sessions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE environments ADD CONSTRAINT FK_CE28A8313A51721D FOREIGN KEY (instance_id) REFERENCES instances (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_instance_types ADD CONSTRAINT FK_4987EEF78DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_instance_types ADD CONSTRAINT FK_4987EEF72D84150 FOREIGN KEY (instance_type_id) REFERENCES instance_types (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE environments_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_instance_types_id_seq CASCADE');
        $this->addSql('DROP TABLE environments');
        $this->addSql('DROP TABLE task_instance_types');
    }
}
