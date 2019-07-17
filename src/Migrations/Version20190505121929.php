<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190505121929 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE global_major_rank (id INT AUTO_INCREMENT NOT NULL, global_major_info_id INT NOT NULL, college_name_cn VARCHAR(100) DEFAULT NULL, college_name_en VARCHAR(100) DEFAULT NULL, rank VARCHAR(50) NOT NULL, country VARCHAR(50) DEFAULT NULL, college_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usmajor_rank (id INT AUTO_INCREMENT NOT NULL, us_major_info_id INT NOT NULL, college_name_cn VARCHAR(100) DEFAULT NULL, college_name_en VARCHAR(100) DEFAULT NULL, rank VARCHAR(50) NOT NULL, college_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usmajor_info (id INT AUTO_INCREMENT NOT NULL, major_name VARCHAR(100) NOT NULL, major_category VARCHAR(100) NOT NULL, rank_category VARCHAR(20) NOT NULL, education VARCHAR(20) NOT NULL, country VARCHAR(50) NOT NULL, major_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE global_major_info (id INT AUTO_INCREMENT NOT NULL, major_name VARCHAR(100) NOT NULL, major_category VARCHAR(100) NOT NULL, rank_category VARCHAR(20) NOT NULL, major_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE global_major_rank');
        $this->addSql('DROP TABLE usmajor_rank');
        $this->addSql('DROP TABLE usmajor_info');
        $this->addSql('DROP TABLE global_major_info');
    }
}
