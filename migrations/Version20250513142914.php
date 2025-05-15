<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513142914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704B9245DE54');
        $this->addSql('DROP INDEX IDX_27BA704B9245DE54 ON history');
        $this->addSql('ALTER TABLE history CHANGE tender_id tender_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE history CHANGE tender_id tender_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704B9245DE54 FOREIGN KEY (tender_id) REFERENCES tender (id)');
        $this->addSql('CREATE INDEX IDX_27BA704B9245DE54 ON history (tender_id)');
    }
}
