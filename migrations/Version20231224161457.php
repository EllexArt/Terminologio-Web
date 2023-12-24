<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231224161457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE component (id INT AUTO_INCREMENT NOT NULL, concept_id INT NOT NULL, position_x INT NOT NULL, position_y INT NOT NULL, number INT NOT NULL, INDEX IDX_49FEA157F909284E (concept_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE component_name (id INT AUTO_INCREMENT NOT NULL, composant_id INT NOT NULL, language_id INT NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_E203C9307F3310E7 (composant_id), INDEX IDX_E203C93082F1BAF4 (language_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE component ADD CONSTRAINT FK_49FEA157F909284E FOREIGN KEY (concept_id) REFERENCES concept (id)');
        $this->addSql('ALTER TABLE component_name ADD CONSTRAINT FK_E203C9307F3310E7 FOREIGN KEY (composant_id) REFERENCES component (id)');
        $this->addSql('ALTER TABLE component_name ADD CONSTRAINT FK_E203C93082F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id)');
        $this->addSql('ALTER TABLE composant DROP FOREIGN KEY FK_EC8486C9F909284E');
        $this->addSql('ALTER TABLE composant_name DROP FOREIGN KEY FK_3BC662EC7F3310E7');
        $this->addSql('ALTER TABLE composant_name DROP FOREIGN KEY FK_3BC662EC82F1BAF4');
        $this->addSql('DROP TABLE composant');
        $this->addSql('DROP TABLE composant_name');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE composant (id INT AUTO_INCREMENT NOT NULL, concept_id INT NOT NULL, position_x INT NOT NULL, position_y INT NOT NULL, number INT NOT NULL, INDEX IDX_EC8486C9F909284E (concept_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE composant_name (id INT AUTO_INCREMENT NOT NULL, composant_id INT NOT NULL, language_id INT NOT NULL, value VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_3BC662EC7F3310E7 (composant_id), INDEX IDX_3BC662EC82F1BAF4 (language_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE composant ADD CONSTRAINT FK_EC8486C9F909284E FOREIGN KEY (concept_id) REFERENCES concept (id)');
        $this->addSql('ALTER TABLE composant_name ADD CONSTRAINT FK_3BC662EC7F3310E7 FOREIGN KEY (composant_id) REFERENCES composant (id)');
        $this->addSql('ALTER TABLE composant_name ADD CONSTRAINT FK_3BC662EC82F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id)');
        $this->addSql('ALTER TABLE component DROP FOREIGN KEY FK_49FEA157F909284E');
        $this->addSql('ALTER TABLE component_name DROP FOREIGN KEY FK_E203C9307F3310E7');
        $this->addSql('ALTER TABLE component_name DROP FOREIGN KEY FK_E203C93082F1BAF4');
        $this->addSql('DROP TABLE component');
        $this->addSql('DROP TABLE component_name');
    }
}
