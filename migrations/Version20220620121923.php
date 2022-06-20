<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220620121923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE session_techs_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE session_techs (id INT NOT NULL, session_id INT NOT NULL, tech_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_334CBAA5613FECDF ON session_techs (session_id)');
        $this->addSql('CREATE INDEX IDX_334CBAA564727BFC ON session_techs (tech_id)');
        $this->addSql('ALTER TABLE session_techs ADD CONSTRAINT FK_334CBAA5613FECDF FOREIGN KEY (session_id) REFERENCES sessions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE session_techs ADD CONSTRAINT FK_334CBAA564727BFC FOREIGN KEY (tech_id) REFERENCES technologies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE session_techs_id_seq CASCADE');
        $this->addSql('DROP TABLE session_techs');
    }
}
