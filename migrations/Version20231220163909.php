<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231220163909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_880E0D76E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A60505602A942');
        $this->addSql('DROP INDEX IDX_E74A60505602A942 ON concept');
        $this->addSql('ALTER TABLE concept DROP default_language_id, DROP title');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE admin');
        $this->addSql('ALTER TABLE concept ADD default_language_id INT NOT NULL, ADD title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A60505602A942 FOREIGN KEY (default_language_id) REFERENCES language (id)');
        $this->addSql('CREATE INDEX IDX_E74A60505602A942 ON concept (default_language_id)');
        $this->addSql('ALTER TABLE user DROP username');
    }
}
