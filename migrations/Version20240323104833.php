<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240323104833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item_price (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, name VARCHAR(255) NOT NULL, examine VARCHAR(255) DEFAULT NULL, avg_price INT DEFAULT NULL, low_price INT DEFAULT NULL, high_price INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stack (id INT AUTO_INCREMENT NOT NULL, item_price_id INT NOT NULL, quantity INT NOT NULL, UNIQUE INDEX UNIQ_41A87B6ABA190F2C (item_price_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stack ADD CONSTRAINT FK_41A87B6ABA190F2C FOREIGN KEY (item_price_id) REFERENCES item_price (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stack DROP FOREIGN KEY FK_41A87B6ABA190F2C');
        $this->addSql('DROP TABLE item_price');
        $this->addSql('DROP TABLE stack');
    }
}
