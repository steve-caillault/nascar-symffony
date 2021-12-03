<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211127171124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cars_models (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, motor SMALLINT UNSIGNED NOT NULL, name VARCHAR(50) NOT NULL, INDEX idx_motor (motor), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cars_models ADD CONSTRAINT FK_E0975B4FCB530440 FOREIGN KEY (motor) REFERENCES motors (id) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cars_models');
    }

    public function isTransactional() : bool
    {
        return false;
    }

}