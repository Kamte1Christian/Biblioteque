<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250314092010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonnement (id INT AUTO_INCREMENT NOT NULL, abonne_id INT NOT NULL, type_id INT NOT NULL, date_debut DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_fin DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', amount DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_351268BBC325A696 (abonne_id), INDEX IDX_351268BBC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, categorie_id INT DEFAULT NULL, classe_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, pages SMALLINT DEFAULT NULL, is_free TINYINT(1) DEFAULT NULL, date_publication DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', description VARCHAR(300) DEFAULT NULL, cover_image VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, book_file VARCHAR(255) DEFAULT NULL, averagescore INT DEFAULT NULL, INDEX IDX_CBE5A331BCF5E72D (categorie_id), INDEX IDX_CBE5A3318F5EA509 (classe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, categorie VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, classe VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emprunt (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, start_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', normal_back_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', effective_back_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_backed TINYINT(1) NOT NULL, INDEX IDX_364071D7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exemplaire (id INT AUTO_INCREMENT NOT NULL, book_id INT NOT NULL, emprunt_id INT DEFAULT NULL, code_bar VARCHAR(255) NOT NULL, state TINYINT(1) NOT NULL, INDEX IDX_5EF83C9216A2B381 (book_id), INDEX IDX_5EF83C92AE7FEF94 (emprunt_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, book_id INT DEFAULT NULL, score INT NOT NULL, INDEX IDX_268BC95A76ED395 (user_id), INDEX IDX_268BC9516A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_abonnement (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, duree_jours INT NOT NULL, UNIQUE INDEX UNIQ_2811BE9E8CDE5729 (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, fname VARCHAR(255) NOT NULL, lname VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE abonnement ADD CONSTRAINT FK_351268BBC325A696 FOREIGN KEY (abonne_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE abonnement ADD CONSTRAINT FK_351268BBC54C8C93 FOREIGN KEY (type_id) REFERENCES type_abonnement (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3318F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE emprunt ADD CONSTRAINT FK_364071D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE exemplaire ADD CONSTRAINT FK_5EF83C9216A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE exemplaire ADD CONSTRAINT FK_5EF83C92AE7FEF94 FOREIGN KEY (emprunt_id) REFERENCES emprunt (id)');
        $this->addSql('ALTER TABLE notation ADD CONSTRAINT FK_268BC95A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notation ADD CONSTRAINT FK_268BC9516A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnement DROP FOREIGN KEY FK_351268BBC325A696');
        $this->addSql('ALTER TABLE abonnement DROP FOREIGN KEY FK_351268BBC54C8C93');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331BCF5E72D');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3318F5EA509');
        $this->addSql('ALTER TABLE emprunt DROP FOREIGN KEY FK_364071D7A76ED395');
        $this->addSql('ALTER TABLE exemplaire DROP FOREIGN KEY FK_5EF83C9216A2B381');
        $this->addSql('ALTER TABLE exemplaire DROP FOREIGN KEY FK_5EF83C92AE7FEF94');
        $this->addSql('ALTER TABLE notation DROP FOREIGN KEY FK_268BC95A76ED395');
        $this->addSql('ALTER TABLE notation DROP FOREIGN KEY FK_268BC9516A2B381');
        $this->addSql('DROP TABLE abonnement');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE emprunt');
        $this->addSql('DROP TABLE exemplaire');
        $this->addSql('DROP TABLE notation');
        $this->addSql('DROP TABLE type_abonnement');
        $this->addSql('DROP TABLE user');
    }
}
