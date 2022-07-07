<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220707204852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX sessions_hash ON sessions (hash)');
        $this->addSql('CREATE UNIQUE INDEX task_instance_types_combo ON task_instance_types (task_id, instance_type_id)');
        $this->addSql('CREATE UNIQUE INDEX tasks_name ON tasks (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX task_instance_types_combo');
        $this->addSql('DROP INDEX tasks_name');
        $this->addSql('DROP INDEX sessions_hash');
    }
}
