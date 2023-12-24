<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231224170804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE concept ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE concept ADD CONSTRAINT FK_E74A605012469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_E74A605012469DE2 ON concept (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE concept DROP FOREIGN KEY FK_E74A605012469DE2');
        $this->addSql('DROP INDEX IDX_E74A605012469DE2 ON concept');
        $this->addSql('ALTER TABLE concept DROP category_id');
    }
}
