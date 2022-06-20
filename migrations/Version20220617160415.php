<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220617160415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE instances_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE instances (id INT NOT NULL, instance_types_id_id INT NOT NULL, status TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, port INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7A270069331416ED ON instances (instance_types_id_id)');
        $this->addSql('COMMENT ON COLUMN instances.status IS \'(DC2Type:simple_array)\'');
        $this->addSql('COMMENT ON COLUMN instances.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE instances ADD CONSTRAINT FK_7A270069331416ED FOREIGN KEY (instance_types_id_id) REFERENCES instance_types (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hardware_profiles ADD cost INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE instances_id_seq CASCADE');
        $this->addSql('DROP TABLE instances');
        $this->addSql('ALTER TABLE hardware_profiles DROP cost');
    }
}
