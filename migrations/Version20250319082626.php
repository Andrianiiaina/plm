<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250319082626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE allotissement (id INT AUTO_INCREMENT NOT NULL, tender_id INT DEFAULT NULL, number VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, min_budget INT DEFAULT NULL, max_budget INT DEFAULT NULL, INDEX IDX_C3228E669245DE54 (tender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE allotissement ADD CONSTRAINT FK_C3228E669245DE54 FOREIGN KEY (tender_id) REFERENCES tender (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE allotissement DROP FOREIGN KEY FK_C3228E669245DE54');
        $this->addSql('DROP TABLE allotissement');
    }
}
