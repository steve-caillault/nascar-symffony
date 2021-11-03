<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210826140404 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pilots (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, birth_city SMALLINT UNSIGNED NOT NULL, public_id VARCHAR(100) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, birthdate DATETIME NOT NULL, INDEX fk_birth_city (birth_city), UNIQUE INDEX idx_public_id (public_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pilots_public_ids (public_id VARCHAR(100) NOT NULL, pilot SMALLINT UNSIGNED NOT NULL, INDEX fk_pilot (pilot), PRIMARY KEY(pilot, public_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pilots ADD CONSTRAINT FK_9EE6E18C62F1E114 FOREIGN KEY (birth_city) REFERENCES cities (id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE pilots_public_ids ADD CONSTRAINT FK_2905B79F8D1E5F52 FOREIGN KEY (pilot) REFERENCES pilots (id) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pilots_public_ids DROP FOREIGN KEY FK_2905B79F8D1E5F52');
        $this->addSql('DROP TABLE pilots');
        $this->addSql('DROP TABLE pilots_public_ids');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
