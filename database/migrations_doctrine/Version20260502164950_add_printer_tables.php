<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260502164950_add_printer_tables extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('CREATE TABLE printer_jobs (job_id INT AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, printer_id INT DEFAULT NULL, job_name VARCHAR(255) NOT NULL, pages_a4_black INT NOT NULL, pages_a4_colour INT NOT NULL, pages_a3_black INT NOT NULL, pages_a3_colour INT NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_C4686304A76ED395 (user_id), INDEX IDX_C468630446EC494A (printer_id), PRIMARY KEY(job_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE printers (printer_id INT AUTO_INCREMENT NOT NULL, printer_name VARCHAR(255) NOT NULL, ipp_uri VARCHAR(255) NOT NULL, cost_a4_black INT DEFAULT NULL, cost_a4_colour INT DEFAULT NULL, cost_a3_black INT DEFAULT NULL, cost_a3_colour INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_C5381DB732D870C7 (printer_name), PRIMARY KEY(printer_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE printer_jobs ADD CONSTRAINT FK_C4686304A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE printer_jobs ADD CONSTRAINT FK_C468630446EC494A FOREIGN KEY (printer_id) REFERENCES printers (printer_id)');
        $this->addSql('ALTER TABLE printer_jobs ADD CONSTRAINT FK_C468630446EC494A FOREIGN KEY (printer_id) REFERENCES printers (printer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE printer_jobs DROP FOREIGN KEY FK_C4686304A76ED395');
        $this->addSql('ALTER TABLE printer_jobs DROP FOREIGN KEY FK_C468630446EC494A');
        $this->addSql('DROP TABLE printer_jobs');
        $this->addSql('DROP TABLE printers');
    }
}
