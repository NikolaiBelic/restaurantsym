<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250212003145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart_food (id INT AUTO_INCREMENT NOT NULL, cart_id INT NOT NULL, food_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_3BDD29CE1AD5CDBF (cart_id), INDEX IDX_3BDD29CEBA8E87C4 (food_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart_food ADD CONSTRAINT FK_3BDD29CE1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE cart_food ADD CONSTRAINT FK_3BDD29CEBA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_food DROP FOREIGN KEY FK_3BDD29CE1AD5CDBF');
        $this->addSql('ALTER TABLE cart_food DROP FOREIGN KEY FK_3BDD29CEBA8E87C4');
        $this->addSql('DROP TABLE cart_food');
    }
}
