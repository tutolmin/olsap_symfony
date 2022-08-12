<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220812111214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE addresses_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ports_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE addresses (id INT NOT NULL, port_id INT NOT NULL, instance_id INT DEFAULT NULL, ip VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6FCA751676E92A9C ON addresses (port_id)');
        $this->addSql('CREATE INDEX IDX_6FCA75163A51721D ON addresses (instance_id)');
        $this->addSql('CREATE TABLE ports (id INT NOT NULL, number INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE addresses ADD CONSTRAINT FK_6FCA751676E92A9C FOREIGN KEY (port_id) REFERENCES ports (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE addresses ADD CONSTRAINT FK_6FCA75163A51721D FOREIGN KEY (instance_id) REFERENCES instances (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE addresses DROP CONSTRAINT FK_6FCA751676E92A9C');
        $this->addSql('DROP SEQUENCE addresses_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ports_id_seq CASCADE');
        $this->addSql('DROP TABLE addresses');
        $this->addSql('DROP TABLE ports');
    }
}
