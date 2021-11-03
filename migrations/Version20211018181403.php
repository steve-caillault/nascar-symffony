<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211018181403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE circuits (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, city SMALLINT UNSIGNED NOT NULL, name VARCHAR(100) NOT NULL, distance SMALLINT UNSIGNED NOT NULL, INDEX fk_city (city), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE circuits ADD CONSTRAINT FK_24A8EC252D5B0234 FOREIGN KEY (city) REFERENCES cities (id) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE circuits');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
