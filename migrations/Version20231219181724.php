<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231219181724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE concept ADD default_language_id INT NOT NULL');
        $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A60505602A942 FOREIGN KEY (default_language_id) REFERENCES language (id)');
        $this->addSql('CREATE INDEX IDX_E74A60505602A942 ON concept (default_language_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A60505602A942');
        $this->addSql('DROP INDEX IDX_E74A60505602A942 ON concept');
        $this->addSql('ALTER TABLE concept DROP default_language_id');
    }
}
