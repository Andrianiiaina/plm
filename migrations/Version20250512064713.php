<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250512064713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tender DROP submission_date, DROP response_date, DROP attribution_date, DROP negociation_date, DROP duration');
        $this->addSql('ALTER TABLE tender_date CHANGE duration duration DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tender ADD submission_date DATETIME DEFAULT NULL, ADD response_date DATETIME DEFAULT NULL, ADD attribution_date DATETIME DEFAULT NULL, ADD negociation_date DATETIME DEFAULT NULL, ADD duration DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE tender_date CHANGE duration duration INT NOT NULL');
    }
}
