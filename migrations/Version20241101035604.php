<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241101035604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonnements (id INT AUTO_INCREMENT NOT NULL, abonné_id INT NOT NULL, type_id INT NOT NULL, date_debut DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_fin DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4788B76748509D39 (abonné_id), INDEX IDX_4788B767C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, categorie VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classes (id INT AUTO_INCREMENT NOT NULL, classe VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emprunts (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, exemplaire_id INT NOT NULL, start_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', normal_back_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', effective_back_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_backed TINYINT(1) NOT NULL, INDEX IDX_38FC80DA76ED395 (user_id), INDEX IDX_38FC80D5843AA21 (exemplaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exemplaires (id INT AUTO_INCREMENT NOT NULL, livre_id INT NOT NULL, code_bar INT NOT NULL, INDEX IDX_551C55F37D925CB (livre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livres (id INT AUTO_INCREMENT NOT NULL, categorie_id INT DEFAULT NULL, classe_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, auteur VARCHAR(255) NOT NULL, date_publication DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', description VARCHAR(300) DEFAULT NULL, INDEX IDX_927187A4BCF5E72D (categorie_id), INDEX IDX_927187A48F5EA509 (classe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_abonnement (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, duree_jours INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, fname VARCHAR(255) NOT NULL, lname VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE abonnements ADD CONSTRAINT FK_4788B76748509D39 FOREIGN KEY (abonné_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE abonnements ADD CONSTRAINT FK_4788B767C54C8C93 FOREIGN KEY (type_id) REFERENCES type_abonnement (id)');
        $this->addSql('ALTER TABLE emprunts ADD CONSTRAINT FK_38FC80DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE emprunts ADD CONSTRAINT FK_38FC80D5843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaires (id)');
        $this->addSql('ALTER TABLE exemplaires ADD CONSTRAINT FK_551C55F37D925CB FOREIGN KEY (livre_id) REFERENCES livres (id)');
        $this->addSql('ALTER TABLE livres ADD CONSTRAINT FK_927187A4BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE livres ADD CONSTRAINT FK_927187A48F5EA509 FOREIGN KEY (classe_id) REFERENCES classes (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnements DROP FOREIGN KEY FK_4788B76748509D39');
        $this->addSql('ALTER TABLE abonnements DROP FOREIGN KEY FK_4788B767C54C8C93');
        $this->addSql('ALTER TABLE emprunts DROP FOREIGN KEY FK_38FC80DA76ED395');
        $this->addSql('ALTER TABLE emprunts DROP FOREIGN KEY FK_38FC80D5843AA21');
        $this->addSql('ALTER TABLE exemplaires DROP FOREIGN KEY FK_551C55F37D925CB');
        $this->addSql('ALTER TABLE livres DROP FOREIGN KEY FK_927187A4BCF5E72D');
        $this->addSql('ALTER TABLE livres DROP FOREIGN KEY FK_927187A48F5EA509');
        $this->addSql('DROP TABLE abonnements');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE classes');
        $this->addSql('DROP TABLE emprunts');
        $this->addSql('DROP TABLE exemplaires');
        $this->addSql('DROP TABLE livres');
        $this->addSql('DROP TABLE type_abonnement');
        $this->addSql('DROP TABLE user');
    }
}
