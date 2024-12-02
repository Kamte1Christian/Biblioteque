<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241128095746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnement ADD amount DOUBLE PRECISION NOT NULL, ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE book ADD frontcontent_url VARCHAR(255) DEFAULT NULL, ADD front_cover_path VARCHAR(255) DEFAULT NULL, ADD backcontent_url VARCHAR(255) DEFAULT NULL, ADD back_cover_path VARCHAR(255) DEFAULT NULL, DROP content_url, DROP file_path');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnement DROP amount, DROP status');
        $this->addSql('ALTER TABLE book ADD content_url VARCHAR(255) DEFAULT NULL, ADD file_path VARCHAR(255) DEFAULT NULL, DROP frontcontent_url, DROP front_cover_path, DROP backcontent_url, DROP back_cover_path');
    }
}
