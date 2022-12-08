<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221207235945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, oeuvre_id INT DEFAULT NULL, valeur INT NOT NULL, user INT DEFAULT NULL, UNIQUE INDEX UNIQ_1323A57588194DE8 (oeuvre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, type_evenement_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, date_debut DATETIME DEFAULT NULL, date_fin DATETIME DEFAULT NULL, INDEX IDX_B26681E88939516 (type_evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement_sponsor (evenement_id INT NOT NULL, sponsor_id INT NOT NULL, INDEX IDX_8289DE08FD02F13 (evenement_id), INDEX IDX_8289DE0812F7FB51 (sponsor_id), PRIMARY KEY(evenement_id, sponsor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sponsor (id INT AUTO_INCREMENT NOT NULL, nom_societe VARCHAR(255) NOT NULL, type_sponsor VARCHAR(255) NOT NULL, tel VARCHAR(255) NOT NULL, montant DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wishlist (id INT AUTO_INCREMENT NOT NULL, user INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A57588194DE8 FOREIGN KEY (oeuvre_id) REFERENCES oeuvre (id)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E88939516 FOREIGN KEY (type_evenement_id) REFERENCES type_evenement (id)');
        $this->addSql('ALTER TABLE evenement_sponsor ADD CONSTRAINT FK_8289DE08FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement_sponsor ADD CONSTRAINT FK_8289DE0812F7FB51 FOREIGN KEY (sponsor_id) REFERENCES sponsor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oeuvre ADD wishlist_id INT DEFAULT NULL, ADD is_favourite TINYINT(1) DEFAULT NULL, ADD slug VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE oeuvre ADD CONSTRAINT FK_35FE2EFEFB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id)');
        $this->addSql('CREATE INDEX IDX_35FE2EFEFB8E54CD ON oeuvre (wishlist_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oeuvre DROP FOREIGN KEY FK_35FE2EFEFB8E54CD');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A57588194DE8');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E88939516');
        $this->addSql('ALTER TABLE evenement_sponsor DROP FOREIGN KEY FK_8289DE08FD02F13');
        $this->addSql('ALTER TABLE evenement_sponsor DROP FOREIGN KEY FK_8289DE0812F7FB51');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE evenement_sponsor');
        $this->addSql('DROP TABLE sponsor');
        $this->addSql('DROP TABLE wishlist');
        $this->addSql('DROP INDEX IDX_35FE2EFEFB8E54CD ON oeuvre');
        $this->addSql('ALTER TABLE oeuvre DROP wishlist_id, DROP is_favourite, DROP slug, DROP created_at, DROP updated_at');
    }
}
