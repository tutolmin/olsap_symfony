<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220812150417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ports ADD address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ports ADD CONSTRAINT FK_899FD0CDF5B7AF75 FOREIGN KEY (address_id) REFERENCES addresses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_899FD0CDF5B7AF75 ON ports (address_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ports DROP CONSTRAINT FK_899FD0CDF5B7AF75');
        $this->addSql('DROP INDEX UNIQ_899FD0CDF5B7AF75');
        $this->addSql('ALTER TABLE ports DROP address_id');
        $this->addSql('CREATE UNIQUE INDEX ports_number ON ports (number)');
        $this->addSql('ALTER TABLE addresses ADD port_id INT NOT NULL');
        $this->addSql('ALTER TABLE addresses ADD CONSTRAINT fk_6fca751676e92a9c FOREIGN KEY (port_id) REFERENCES ports (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX addresses_ip ON addresses (ip)');
        $this->addSql('CREATE UNIQUE INDEX uniq_6fca751676e92a9c ON addresses (port_id)');
    }
}
