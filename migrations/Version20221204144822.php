<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221204144822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE wishlist (id INT AUTO_INCREMENT NOT NULL, user INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE oeuvre ADD wishlist_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE oeuvre ADD CONSTRAINT FK_35FE2EFEFB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id)');
        $this->addSql('CREATE INDEX IDX_35FE2EFEFB8E54CD ON oeuvre (wishlist_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oeuvre DROP FOREIGN KEY FK_35FE2EFEFB8E54CD');
        $this->addSql('DROP TABLE wishlist');
        $this->addSql('DROP INDEX IDX_35FE2EFEFB8E54CD ON oeuvre');
        $this->addSql('ALTER TABLE oeuvre DROP wishlist_id');
    }
}
