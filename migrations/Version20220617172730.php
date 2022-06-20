<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220617172730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE session_oses_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE session_oses (id INT NOT NULL, session_id INT NOT NULL, os_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_341510E613FECDF ON session_oses (session_id)');
        $this->addSql('CREATE INDEX IDX_341510E3DCA04D1 ON session_oses (os_id)');
        $this->addSql('ALTER TABLE session_oses ADD CONSTRAINT FK_341510E613FECDF FOREIGN KEY (session_id) REFERENCES sessions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE session_oses ADD CONSTRAINT FK_341510E3DCA04D1 FOREIGN KEY (os_id) REFERENCES operating_systems (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE session_oses_id_seq CASCADE');
        $this->addSql('DROP TABLE session_oses');
    }
}
