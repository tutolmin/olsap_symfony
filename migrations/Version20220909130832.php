<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220909130832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE environments ADD hash VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE UNIQUE INDEX ports_number ON ports (number)');
        $this->addSql('ALTER TABLE addresses ALTER ip TYPE VARCHAR(16)');
        $this->addSql('CREATE UNIQUE INDEX addresses_mac ON addresses (mac)');
        $this->addSql('CREATE UNIQUE INDEX addresses_ip ON addresses (ip)');
        $this->addSql('ALTER TABLE environments DROP hash');
        $this->addSql('ALTER TABLE hardware_profiles ALTER supported SET DEFAULT false');
        $this->addSql('CREATE UNIQUE INDEX tasks_verify ON tasks (verify)');
        $this->addSql('CREATE UNIQUE INDEX tasks_solve ON tasks (solve)');
        $this->addSql('CREATE UNIQUE INDEX tasks_deploy ON tasks (deploy)');
        $this->addSql('CREATE UNIQUE INDEX tasks_project ON tasks (project)');
    }
}
