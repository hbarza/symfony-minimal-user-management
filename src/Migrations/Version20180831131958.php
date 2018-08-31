<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180831131958 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_entity (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(64) NOT NULL, firstname VARCHAR(32) NOT NULL, lastname VARCHAR(32) DEFAULT NULL, create_at DATETIME NOT NULL, update_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) DEFAULT NULL, create_at DATETIME NOT NULL, update_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group_user_entity (user_group_id INT NOT NULL, user_entity_id INT NOT NULL, INDEX IDX_B6DDFBBF1ED93D47 (user_group_id), INDEX IDX_B6DDFBBF81C5F0B9 (user_entity_id), PRIMARY KEY(user_group_id, user_entity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_group_user_entity ADD CONSTRAINT FK_B6DDFBBF1ED93D47 FOREIGN KEY (user_group_id) REFERENCES user_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_user_entity ADD CONSTRAINT FK_B6DDFBBF81C5F0B9 FOREIGN KEY (user_entity_id) REFERENCES user_entity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_group_user_entity DROP FOREIGN KEY FK_B6DDFBBF81C5F0B9');
        $this->addSql('ALTER TABLE user_group_user_entity DROP FOREIGN KEY FK_B6DDFBBF1ED93D47');
        $this->addSql('DROP TABLE user_entity');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('DROP TABLE user_group_user_entity');
    }
}
