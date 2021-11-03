<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211102195909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE motors (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, public_id VARCHAR(100) NOT NULL, name VARCHAR(20) NOT NULL, UNIQUE INDEX idx_public_id (public_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE motors_public_ids (public_id VARCHAR(100) NOT NULL, motor SMALLINT UNSIGNED NOT NULL, INDEX fk_motor (motor), PRIMARY KEY(motor, public_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE motors_public_ids ADD CONSTRAINT FK_54756801CB530440 FOREIGN KEY (motor) REFERENCES motors (id) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE motors_public_ids DROP FOREIGN KEY FK_54756801CB530440');
        $this->addSql('DROP TABLE motors');
        $this->addSql('DROP TABLE motors_public_ids');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
