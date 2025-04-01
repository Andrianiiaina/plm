<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318060627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tender ADD contact_id INT DEFAULT NULL, ADD submission_date DATETIME DEFAULT NULL, ADD response_date DATETIME DEFAULT NULL, ADD attribution_date DATETIME DEFAULT NULL, ADD negociation_date DATETIME DEFAULT NULL, CHANGE status status VARCHAR(255) NOT NULL, CHANGE tender_type tender_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tender ADD CONSTRAINT FK_42057A77E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42057A77E7A1254A ON tender (contact_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tender DROP FOREIGN KEY FK_42057A77E7A1254A');
        $this->addSql('DROP INDEX UNIQ_42057A77E7A1254A ON tender');
        $this->addSql('ALTER TABLE tender DROP contact_id, DROP submission_date, DROP response_date, DROP attribution_date, DROP negociation_date, CHANGE status status INT NOT NULL, CHANGE tender_type tender_type INT NOT NULL');
    }
}
