<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250625124735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tender_contact (tender_id INT NOT NULL, contact_id INT NOT NULL, INDEX IDX_A4598A339245DE54 (tender_id), INDEX IDX_A4598A33E7A1254A (contact_id), PRIMARY KEY(tender_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tender_contact ADD CONSTRAINT FK_A4598A339245DE54 FOREIGN KEY (tender_id) REFERENCES tender (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tender_contact ADD CONSTRAINT FK_A4598A33E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tender DROP FOREIGN KEY FK_42057A77E7A1254A');
        $this->addSql('DROP INDEX UNIQ_42057A77E7A1254A ON tender');
        $this->addSql('ALTER TABLE tender DROP contact_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tender_contact DROP FOREIGN KEY FK_A4598A339245DE54');
        $this->addSql('ALTER TABLE tender_contact DROP FOREIGN KEY FK_A4598A33E7A1254A');
        $this->addSql('DROP TABLE tender_contact');
        $this->addSql('ALTER TABLE tender ADD contact_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tender ADD CONSTRAINT FK_42057A77E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42057A77E7A1254A ON tender (contact_id)');
    }
}
