<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220617164934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE testee_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE testee (id INT NOT NULL, email VARCHAR(255) NOT NULL, oath_token VARCHAR(255) NOT NULL, registered_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN testee.registered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE sessions ADD testee_id INT NOT NULL');
        $this->addSql('ALTER TABLE sessions ADD CONSTRAINT FK_9A609D135A544EE7 FOREIGN KEY (testee_id) REFERENCES testee (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9A609D135A544EE7 ON sessions (testee_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE sessions DROP CONSTRAINT FK_9A609D135A544EE7');
        $this->addSql('DROP SEQUENCE testee_id_seq CASCADE');
        $this->addSql('DROP TABLE testee');
        $this->addSql('DROP INDEX IDX_9A609D135A544EE7');
        $this->addSql('ALTER TABLE sessions DROP testee_id');
    }
}
