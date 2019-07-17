<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190629062020 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE college_email_domain (id INT AUTO_INCREMENT NOT NULL, college_id INT NOT NULL, domain VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE college_repo CHANGE college_id college_id INT NOT NULL, CHANGE sort sort INT NOT NULL');
        $this->addSql('ALTER TABLE user_education ADD verifiy_email VARCHAR(100) DEFAULT NULL AFTER `status`');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE college_email_domain');
        $this->addSql('ALTER TABLE college_repo CHANGE college_id college_id INT DEFAULT NULL, CHANGE sort sort INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_education DROP verifiy_email');
    }
}
