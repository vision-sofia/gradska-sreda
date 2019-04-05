<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190405181745 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE x_survey.ev_criterion_subject_metadata (criterion_subject_id INT NOT NULL, max_points NUMERIC(3, 1) NOT NULL, PRIMARY KEY(criterion_subject_id))');
        $this->addSql('ALTER TABLE x_survey.ev_criterion_subject_metadata ADD CONSTRAINT FK_EA32F368A62DDE37 FOREIGN KEY (criterion_subject_id) REFERENCES x_survey.ev_criterion_subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.ev_criterion_subject ADD metadata JSONB DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN x_survey.ev_criterion_subject.metadata IS \'(DC2Type:json_array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE x_survey.ev_criterion_subject_metadata');
        $this->addSql('ALTER TABLE x_survey.ev_criterion_subject DROP metadata');
    }
}
