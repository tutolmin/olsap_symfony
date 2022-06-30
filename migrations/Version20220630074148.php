<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220630074148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE breed_id_seq CASCADE');
        $this->addSql('ALTER TABLE operating_systems ADD breed_id INT NOT NULL');
        $this->addSql('ALTER TABLE operating_systems ADD CONSTRAINT FK_810D851AA8B4A30F FOREIGN KEY (breed_id) REFERENCES breeds (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_810D851AA8B4A30F ON operating_systems (breed_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE breed_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE operating_systems DROP CONSTRAINT FK_810D851AA8B4A30F');
        $this->addSql('DROP INDEX IDX_810D851AA8B4A30F');
        $this->addSql('ALTER TABLE operating_systems DROP breed_id');
    }
}
