<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250512062057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tender_date (id INT AUTO_INCREMENT NOT NULL, tender_id INT DEFAULT NULL, submission_date DATETIME DEFAULT NULL, response_date DATETIME DEFAULT NULL, attribution_date DATETIME DEFAULT NULL, negociation_date DATETIME DEFAULT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_7C4FD42A9245DE54 (tender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tender_date ADD CONSTRAINT FK_7C4FD42A9245DE54 FOREIGN KEY (tender_id) REFERENCES tender (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tender_date DROP FOREIGN KEY FK_7C4FD42A9245DE54');
        $this->addSql('DROP TABLE tender_date');
    }
}
