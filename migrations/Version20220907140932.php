<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220907140932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tasks ADD project INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tasks ADD solve INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tasks ADD deploy INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tasks ADD verify INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE UNIQUE INDEX ports_number ON ports (number)');
        $this->addSql('ALTER TABLE tasks DROP project');
        $this->addSql('ALTER TABLE tasks DROP solve');
        $this->addSql('ALTER TABLE tasks DROP deploy');
        $this->addSql('ALTER TABLE tasks DROP verify');
        $this->addSql('ALTER TABLE addresses ALTER ip TYPE VARCHAR(16)');
        $this->addSql('CREATE UNIQUE INDEX addresses_mac ON addresses (mac)');
        $this->addSql('CREATE UNIQUE INDEX addresses_ip ON addresses (ip)');
        $this->addSql('ALTER TABLE hardware_profiles ALTER supported SET DEFAULT false');
    }
}
