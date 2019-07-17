<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190605064120 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, college_id INT NOT NULL, author_id INT NOT NULL, education VARCHAR(50) NOT NULL, major_en VARCHAR(100) NOT NULL, major_cn VARCHAR(100) NOT NULL, category VARCHAR(100) NOT NULL, doc_type VARCHAR(100) NOT NULL, doc_word_num VARCHAR(100) NOT NULL, doc_tips LONGTEXT DEFAULT NULL, doc_content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE docment_author (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, nickname VARCHAR(100) NOT NULL, session VARCHAR(100) NOT NULL, major VARCHAR(100) NOT NULL, name_cn VARCHAR(100) NOT NULL, name_en VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE docment_author');
    }
}
