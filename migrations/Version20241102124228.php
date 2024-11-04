<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241102124228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE emprunt_exemplaire (id INT AUTO_INCREMENT NOT NULL, emprunt_id INT NOT NULL, exemplaire_id INT NOT NULL, normal_back_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', effctive_back_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9F6C8689AE7FEF94 (emprunt_id), INDEX IDX_9F6C86895843AA21 (exemplaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE emprunt_exemplaire ADD CONSTRAINT FK_9F6C8689AE7FEF94 FOREIGN KEY (emprunt_id) REFERENCES emprunts (id)');
        $this->addSql('ALTER TABLE emprunt_exemplaire ADD CONSTRAINT FK_9F6C86895843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaires (id)');
        $this->addSql('ALTER TABLE emprunts DROP FOREIGN KEY FK_38FC80D5843AA21');
        $this->addSql('DROP INDEX IDX_38FC80D5843AA21 ON emprunts');
        $this->addSql('ALTER TABLE emprunts DROP exemplaire_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE emprunt_exemplaire DROP FOREIGN KEY FK_9F6C8689AE7FEF94');
        $this->addSql('ALTER TABLE emprunt_exemplaire DROP FOREIGN KEY FK_9F6C86895843AA21');
        $this->addSql('DROP TABLE emprunt_exemplaire');
        $this->addSql('ALTER TABLE emprunts ADD exemplaire_id INT NOT NULL');
        $this->addSql('ALTER TABLE emprunts ADD CONSTRAINT FK_38FC80D5843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaires (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_38FC80D5843AA21 ON emprunts (exemplaire_id)');
    }
}
