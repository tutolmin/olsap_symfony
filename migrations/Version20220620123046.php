<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220620123046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE task_techs_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE task_techs (id INT NOT NULL, task_id INT NOT NULL, tech_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2F9519538DB60186 ON task_techs (task_id)');
        $this->addSql('CREATE INDEX IDX_2F95195364727BFC ON task_techs (tech_id)');
        $this->addSql('ALTER TABLE task_techs ADD CONSTRAINT FK_2F9519538DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_techs ADD CONSTRAINT FK_2F95195364727BFC FOREIGN KEY (tech_id) REFERENCES technologies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE task_techs_id_seq CASCADE');
        $this->addSql('DROP TABLE task_techs');
    }
}
