<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221114165026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, adresse_livraison VARCHAR(255) NOT NULL, tel VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande_oeuvre (commande_id INT NOT NULL, oeuvre_id INT NOT NULL, INDEX IDX_37AAD52B82EA2E54 (commande_id), INDEX IDX_37AAD52B88194DE8 (oeuvre_id), PRIMARY KEY(commande_id, oeuvre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande_oeuvre ADD CONSTRAINT FK_37AAD52B82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande_oeuvre ADD CONSTRAINT FK_37AAD52B88194DE8 FOREIGN KEY (oeuvre_id) REFERENCES oeuvre (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande_oeuvre DROP FOREIGN KEY FK_37AAD52B82EA2E54');
        $this->addSql('ALTER TABLE commande_oeuvre DROP FOREIGN KEY FK_37AAD52B88194DE8');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE commande_oeuvre');
    }
}
