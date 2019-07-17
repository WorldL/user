<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190326115029 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE college_art (id INT AUTO_INCREMENT NOT NULL, college_id INT NOT NULL, sia_acceptance_rate VARCHAR(100) DEFAULT NULL, average_acceptance_rate VARCHAR(100) DEFAULT NULL, apply_deadline VARCHAR(100) DEFAULT NULL, apply_difficulty VARCHAR(100) DEFAULT NULL, total_fee VARCHAR(100) DEFAULT NULL, education_fee VARCHAR(100) DEFAULT NULL, book_fee VARCHAR(100) DEFAULT NULL, boarding_fee VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE college_graduate (id INT AUTO_INCREMENT NOT NULL, college_id INT NOT NULL, apply_deadline VARCHAR(255) DEFAULT NULL, total_fee VARCHAR(100) DEFAULT NULL, apply_fee VARCHAR(100) DEFAULT NULL, education_fee VARCHAR(100) DEFAULT NULL, book_fee VARCHAR(100) DEFAULT NULL, boarding_fee VARCHAR(100) DEFAULT NULL, toefl_score VARCHAR(100) DEFAULT NULL, ielts_score VARCHAR(100) DEFAULT NULL, gre_score VARCHAR(100) DEFAULT NULL, sat_score VARCHAR(100) DEFAULT NULL, gmat_score VARCHAR(100) DEFAULT NULL, gpa VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE college (id INT AUTO_INCREMENT NOT NULL, name_cn VARCHAR(255) NOT NULL, name_en VARCHAR(255) NOT NULL, region VARCHAR(255) NOT NULL, tuition_fee_undergraduate VARCHAR(255) DEFAULT NULL, category VARCHAR(32) NOT NULL, toefl_undergraduate INT DEFAULT NULL, ielts_undergraduate DOUBLE PRECISION DEFAULT NULL, introduction VARCHAR(1000) DEFAULT NULL, pro_subject VARCHAR(500) DEFAULT NULL, student_faculty_ratio VARCHAR(255) DEFAULT NULL, applications INT DEFAULT NULL, enrollment INT DEFAULT NULL, actual_enrollment INT DEFAULT NULL, employment VARCHAR(100) DEFAULT NULL, starting_salary VARCHAR(100) DEFAULT NULL, male_female_ratio VARCHAR(100) DEFAULT NULL, total_amount_students INT DEFAULT NULL, total_amount_undergraduate INT DEFAULT NULL, total_amount_graduate INT DEFAULT NULL, establish_date VARCHAR(50) DEFAULT NULL, type VARCHAR(50) NOT NULL, register_office_info VARCHAR(500) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE college_undergraduate (id INT AUTO_INCREMENT NOT NULL, college_id INT NOT NULL, apply_deadline VARCHAR(255) DEFAULT NULL, offer_distribution_date VARCHAR(255) DEFAULT NULL, total_fee VARCHAR(100) DEFAULT NULL, apply_fee VARCHAR(100) DEFAULT NULL, education_fee VARCHAR(100) DEFAULT NULL, boarding_fee VARCHAR(100) DEFAULT NULL, living_fee VARCHAR(100) DEFAULT NULL, book_fee VARCHAR(100) DEFAULT NULL, toefl_score VARCHAR(100) DEFAULT NULL, ielts_score VARCHAR(100) DEFAULT NULL, sat_score VARCHAR(100) DEFAULT NULL, sat2_score VARCHAR(100) DEFAULT NULL, act_score VARCHAR(100) DEFAULT NULL, gpa VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE college_race (id INT AUTO_INCREMENT NOT NULL, college_id INT NOT NULL, diploma VARCHAR(50) NOT NULL, caucasian DOUBLE PRECISION DEFAULT NULL, aferican DOUBLE PRECISION DEFAULT NULL, latino DOUBLE PRECISION DEFAULT NULL, asian DOUBLE PRECISION DEFAULT NULL, international_student DOUBLE PRECISION DEFAULT NULL, other DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE college_crime (id INT AUTO_INCREMENT NOT NULL, college_id INT NOT NULL, year VARCHAR(10) NOT NULL, gunmen_arrested INT DEFAULT NULL, drug_arrested INT DEFAULT NULL, drunk_arrested INT DEFAULT NULL, gunmen_recorded INT DEFAULT NULL, drug_recorded INT DEFAULT NULL, drunk_recorded INT DEFAULT NULL, domestic_violence INT DEFAULT NULL, dating_crime INT DEFAULT NULL, track INT DEFAULT NULL, murder INT DEFAULT NULL, manslaughter INT DEFAULT NULL, rape INT DEFAULT NULL, sexual_harassment INT DEFAULT NULL, incest INT DEFAULT NULL, robbery INT DEFAULT NULL, assault INT DEFAULT NULL, steal INT DEFAULT NULL, vehicle_steal INT DEFAULT NULL, incendiary INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE college_art');
        $this->addSql('DROP TABLE college_graduate');
        $this->addSql('DROP TABLE college');
        $this->addSql('DROP TABLE college_undergraduate');
        $this->addSql('DROP TABLE college_race');
        $this->addSql('DROP TABLE college_crime');
    }
}
