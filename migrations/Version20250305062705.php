<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250305062705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE calendar (id INT AUTO_INCREMENT NOT NULL, tender_id INT DEFAULT NULL, begin_at DATETIME NOT NULL, end_at DATETIME DEFAULT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_6EA9A1469245DE54 (tender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, user_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, contact_perso VARCHAR(100) DEFAULT NULL, contact_pro VARCHAR(100) DEFAULT NULL, organisation VARCHAR(100) NOT NULL, function VARCHAR(100) NOT NULL, INDEX IDX_4C62E638727ACA70 (parent_id), UNIQUE INDEX UNIQ_4C62E638A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_group_contact (contact_group_id INT NOT NULL, contact_id INT NOT NULL, INDEX IDX_EBFAF1B3647145D0 (contact_group_id), INDEX IDX_EBFAF1B3E7A1254A (contact_id), PRIMARY KEY(contact_group_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, responsable_id INT DEFAULT NULL, tender_id INT NOT NULL, filename VARCHAR(255) NOT NULL, filepath VARCHAR(255) NOT NULL, information LONGTEXT DEFAULT NULL, status VARCHAR(10) NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_D8698A7653C59D72 (responsable_id), INDEX IDX_D8698A769245DE54 (tender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, filename VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, filepath VARCHAR(255) NOT NULL, INDEX IDX_8C9F3610166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tender (id INT AUTO_INCREMENT NOT NULL, responsable_id_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, contract_number VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, start_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', min_budget DOUBLE PRECISION NOT NULL, max_budget DOUBLE PRECISION DEFAULT NULL, status INT NOT NULL, tender_type INT NOT NULL, url VARCHAR(255) DEFAULT NULL, INDEX IDX_42057A771ED5BB35 (responsable_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A1469245DE54 FOREIGN KEY (tender_id) REFERENCES tender (id)');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638727ACA70 FOREIGN KEY (parent_id) REFERENCES contact (id)');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contact_group_contact ADD CONSTRAINT FK_EBFAF1B3647145D0 FOREIGN KEY (contact_group_id) REFERENCES contact_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact_group_contact ADD CONSTRAINT FK_EBFAF1B3E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7653C59D72 FOREIGN KEY (responsable_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A769245DE54 FOREIGN KEY (tender_id) REFERENCES tender (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610166D1F9C FOREIGN KEY (project_id) REFERENCES tender (id)');
        $this->addSql('ALTER TABLE tender ADD CONSTRAINT FK_42057A771ED5BB35 FOREIGN KEY (responsable_id_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A1469245DE54');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638727ACA70');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638A76ED395');
        $this->addSql('ALTER TABLE contact_group_contact DROP FOREIGN KEY FK_EBFAF1B3647145D0');
        $this->addSql('ALTER TABLE contact_group_contact DROP FOREIGN KEY FK_EBFAF1B3E7A1254A');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A7653C59D72');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A769245DE54');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610166D1F9C');
        $this->addSql('ALTER TABLE tender DROP FOREIGN KEY FK_42057A771ED5BB35');
        $this->addSql('DROP TABLE calendar');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE contact_group');
        $this->addSql('DROP TABLE contact_group_contact');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE tender');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
