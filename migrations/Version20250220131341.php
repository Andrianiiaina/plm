<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220131341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36106C1197C9');
        $this->addSql('DROP INDEX IDX_8C9F36106C1197C9 ON file');
        $this->addSql('ALTER TABLE file CHANGE project_id_id project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_8C9F3610166D1F9C ON file (project_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610166D1F9C');
        $this->addSql('DROP INDEX IDX_8C9F3610166D1F9C ON file');
        $this->addSql('ALTER TABLE file CHANGE project_id project_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36106C1197C9 FOREIGN KEY (project_id_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_8C9F36106C1197C9 ON file (project_id_id)');
    }
}
