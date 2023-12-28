<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231228103337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE concept ADD author_id INT NOT NULL, ADD is_validated TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A6050F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E74A6050F675F31B ON concept (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A6050F675F31B');
        $this->addSql('DROP INDEX IDX_E74A6050F675F31B ON concept');
        $this->addSql('ALTER TABLE concept DROP author_id, DROP is_validated');
    }
}
