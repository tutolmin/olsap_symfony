<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220830055509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX addresses_ip');
        $this->addSql('ALTER TABLE addresses ADD mac VARCHAR(18) NOT NULL');
        $this->addSql('ALTER TABLE hardware_profiles ALTER supported DROP DEFAULT');
        $this->addSql('ALTER TABLE instances ALTER port SET NOT NULL');
        $this->addSql('DROP INDEX ports_number');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE instances ALTER port DROP NOT NULL');
        $this->addSql('ALTER TABLE addresses DROP mac');
        $this->addSql('CREATE UNIQUE INDEX addresses_ip ON addresses (ip)');
        $this->addSql('CREATE UNIQUE INDEX ports_number ON ports (number)');
        $this->addSql('ALTER TABLE hardware_profiles ALTER supported SET DEFAULT false');
    }
}
