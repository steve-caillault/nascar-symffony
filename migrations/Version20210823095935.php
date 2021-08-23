<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210823095935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE countries_states (code VARCHAR(3) NOT NULL, country_code VARCHAR(2) NOT NULL, image VARCHAR(100) DEFAULT NULL, name VARCHAR(100) NOT NULL, INDEX fk_country (country_code), PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE countries_states ADD CONSTRAINT FK_BD1B9CB3F026BB7C FOREIGN KEY (country_code) REFERENCES countries (code) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE countries_states');
    }
}
