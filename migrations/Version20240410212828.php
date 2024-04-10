<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240410212828 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE price ADD min_high INT DEFAULT NULL, ADD max_high INT DEFAULT NULL, ADD min_low INT DEFAULT NULL, ADD max_low INT DEFAULT NULL, DROP historic_high, DROP historic_low');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE price ADD historic_high INT DEFAULT NULL, ADD historic_low INT DEFAULT NULL, DROP min_high, DROP max_high, DROP min_low, DROP max_low');
    }
}
