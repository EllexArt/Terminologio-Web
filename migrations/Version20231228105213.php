<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231228105213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->addSql('INSERT INTO user (id, email, name, roles, passwd) VALUES (1, "root@terminologio.com", "root", "[\\"ROLE_ADMIN\\"]", "$2y$13$HpW7a6spydV0iq8WS1ZBxuu75dhXmx.wNwdth8L8A9DuuBcDoEau6"');
        $this->connection->insert('user', ['id' => 1, 'email' => 'root@terminologio.com', 'username' => 'root',
            'roles' => '["ROLE_ADMIN"]', 'password' => '$2y$13$HpW7a6spydV0iq8WS1ZBxuu75dhXmx.wNwdth8L8A9DuuBcDoEau6']);
        $this->connection->insert('language', ['id' => 1, 'name' => 'English']);
        $this->connection->insert('category', ['id' => 1, 'name' => 'Miscellaneous']);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
