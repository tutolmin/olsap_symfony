<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220613150459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE hardware_profile_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE hardware_profiles_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE instance_types_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE hardware_profiles (id INT NOT NULL, type BOOLEAN NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE instance_types (id INT NOT NULL, os_id INT NOT NULL, hw_profile_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_55AE98583DCA04D1 ON instance_types (os_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_55AE9858CF6074E6 ON instance_types (hw_profile_id)');
        $this->addSql('ALTER TABLE instance_types ADD CONSTRAINT FK_55AE98583DCA04D1 FOREIGN KEY (os_id) REFERENCES operating_systems (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE instance_types ADD CONSTRAINT FK_55AE9858CF6074E6 FOREIGN KEY (hw_profile_id) REFERENCES hardware_profiles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE instance_types DROP CONSTRAINT FK_55AE9858CF6074E6');
        $this->addSql('DROP SEQUENCE hardware_profiles_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE instance_types_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE hardware_profile_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP TABLE hardware_profiles');
        $this->addSql('DROP TABLE instance_types');
    }
}
