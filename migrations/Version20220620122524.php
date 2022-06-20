<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220620122524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE task_oses_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tasks_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE task_oses (id INT NOT NULL, task_id INT NOT NULL, os_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B683C55F8DB60186 ON task_oses (task_id)');
        $this->addSql('CREATE INDEX IDX_B683C55F3DCA04D1 ON task_oses (os_id)');
        $this->addSql('CREATE TABLE tasks (id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, path VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE task_oses ADD CONSTRAINT FK_B683C55F8DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_oses ADD CONSTRAINT FK_B683C55F3DCA04D1 FOREIGN KEY (os_id) REFERENCES operating_systems (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE task_oses DROP CONSTRAINT FK_B683C55F8DB60186');
        $this->addSql('DROP SEQUENCE task_oses_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tasks_id_seq CASCADE');
        $this->addSql('DROP TABLE task_oses');
        $this->addSql('DROP TABLE tasks');
    }
}
