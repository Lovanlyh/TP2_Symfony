<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251107171614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE infraction DROP FOREIGN KEY FK_C1A458F5296CD8AE');
        $this->addSql('ALTER TABLE infraction DROP FOREIGN KEY FK_C1A458F5C3423909');
        $this->addSql('ALTER TABLE infraction CHANGE type type VARCHAR(20) NOT NULL, CHANGE occurred_at occurred_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE infraction ADD CONSTRAINT FK_C1A458F5296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE infraction ADD CONSTRAINT FK_C1A458F5C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C4E0A61F5E237E06 ON team (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE infraction DROP FOREIGN KEY FK_C1A458F5C3423909');
        $this->addSql('ALTER TABLE infraction DROP FOREIGN KEY FK_C1A458F5296CD8AE');
        $this->addSql('ALTER TABLE infraction CHANGE type type VARCHAR(255) NOT NULL, CHANGE occurred_at occurred_at DATETIME NOT NULL, CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE infraction ADD CONSTRAINT FK_C1A458F5C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE infraction ADD CONSTRAINT FK_C1A458F5296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP INDEX UNIQ_C4E0A61F5E237E06 ON team');
    }
}
