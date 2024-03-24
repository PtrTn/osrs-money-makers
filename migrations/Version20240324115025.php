<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240324115025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item_price (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, name VARCHAR(255) NOT NULL, examine VARCHAR(255) DEFAULT NULL, avg_price INT DEFAULT NULL, low_price INT DEFAULT NULL, high_price INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe (id INT AUTO_INCREMENT NOT NULL, dish_id INT NOT NULL, name VARCHAR(255) NOT NULL, throughput INT NOT NULL, INDEX IDX_DA88B137148EB0CB (dish_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_stack (recipe_id INT NOT NULL, stack_id INT NOT NULL, INDEX IDX_57BBAF7559D8A214 (recipe_id), INDEX IDX_57BBAF7537C70060 (stack_id), PRIMARY KEY(recipe_id, stack_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stack (id INT AUTO_INCREMENT NOT NULL, item_price_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_41A87B6ABA190F2C (item_price_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B137148EB0CB FOREIGN KEY (dish_id) REFERENCES stack (id)');
        $this->addSql('ALTER TABLE recipe_stack ADD CONSTRAINT FK_57BBAF7559D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe_stack ADD CONSTRAINT FK_57BBAF7537C70060 FOREIGN KEY (stack_id) REFERENCES stack (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stack ADD CONSTRAINT FK_41A87B6ABA190F2C FOREIGN KEY (item_price_id) REFERENCES item_price (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B137148EB0CB');
        $this->addSql('ALTER TABLE recipe_stack DROP FOREIGN KEY FK_57BBAF7559D8A214');
        $this->addSql('ALTER TABLE recipe_stack DROP FOREIGN KEY FK_57BBAF7537C70060');
        $this->addSql('ALTER TABLE stack DROP FOREIGN KEY FK_41A87B6ABA190F2C');
        $this->addSql('DROP TABLE item_price');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('DROP TABLE recipe_stack');
        $this->addSql('DROP TABLE stack');
    }
}
