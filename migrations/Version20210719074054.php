<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210719074054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE site_logs (id INT UNSIGNED AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, site_name VARCHAR(100) NOT NULL, uri VARCHAR(255) DEFAULT NULL, level VARCHAR(20) NOT NULL, message TEXT NOT NULL, user_agent VARCHAR(255) DEFAULT NULL, INDEX idx_date (date), INDEX idx_site_name (site_name), INDEX idx_uri (uri), INDEX idx_level (level), INDEX idx_message (message(255)), INDEX idx_user_agent (user_agent), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, public_id VARCHAR(50) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, password_hashed VARCHAR(150) NOT NULL, permissions LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX idx_public_id (public_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE site_logs');
        $this->addSql('DROP TABLE users');
    }
}
