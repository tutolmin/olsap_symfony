<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220613154746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE instance_types ADD CONSTRAINT FK_55AE9858CF6074E6 FOREIGN KEY (hw_profile_id) REFERENCES hardware_profiles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_55AE9858CF6074E6 ON instance_types (hw_profile_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE instance_types DROP CONSTRAINT FK_55AE9858CF6074E6');
        $this->addSql('DROP INDEX IDX_55AE9858CF6074E6');
    }
}
