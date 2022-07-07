<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220707204125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX breeds_name ON breeds (name)');
        $this->addSql('ALTER INDEX domain_name RENAME TO domains_name');
        $this->addSql('CREATE UNIQUE INDEX environments_statuses_status ON environment_statuses (status)');
        $this->addSql('CREATE UNIQUE INDEX hardware_profiles_name ON hardware_profiles (name)');
        $this->addSql('CREATE UNIQUE INDEX instance_statuses_status ON instance_statuses (status)');
        $this->addSql('CREATE UNIQUE INDEX instance_types_combo ON instance_types (hw_profile_id, os_id)');
        $this->addSql('CREATE UNIQUE INDEX instances_name ON instances (name)');
        $this->addSql('CREATE UNIQUE INDEX operating_systems_combo ON operating_systems (breed_id, release)');
        $this->addSql('CREATE UNIQUE INDEX session_oses_combo ON session_oses (session_id, os_id)');
        $this->addSql('CREATE UNIQUE INDEX session_statuses_status ON session_statuses (status)');
        $this->addSql('CREATE UNIQUE INDEX session_techs_combo ON session_techs (session_id, tech_id)');
        $this->addSql('CREATE UNIQUE INDEX task_oses_combo ON task_oses (task_id, os_id)');
        $this->addSql('CREATE UNIQUE INDEX task_techs_combo ON task_techs (task_id, tech_id)');
        $this->addSql('CREATE UNIQUE INDEX technologies_name ON technologies (name)');
        $this->addSql('CREATE UNIQUE INDEX testees_oauth_token ON testees (oauth_token)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX instances_name');
        $this->addSql('ALTER INDEX domains_name RENAME TO domain_name');
        $this->addSql('DROP INDEX technologies_name');
        $this->addSql('DROP INDEX session_oses_combo');
        $this->addSql('DROP INDEX session_techs_combo');
        $this->addSql('DROP INDEX operating_systems_combo');
        $this->addSql('DROP INDEX breeds_name');
        $this->addSql('DROP INDEX environments_statuses_status');
        $this->addSql('DROP INDEX task_oses_combo');
        $this->addSql('DROP INDEX instance_statuses_status');
        $this->addSql('DROP INDEX session_statuses_status');
        $this->addSql('DROP INDEX instance_types_combo');
        $this->addSql('DROP INDEX task_techs_combo');
        $this->addSql('DROP INDEX testees_oauth_token');
        $this->addSql('DROP INDEX hardware_profiles_name');
    }
}
