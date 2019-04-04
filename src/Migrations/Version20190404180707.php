<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190404180707 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX x_survey.uniq_90a3f11ea62dde3782127c22a76ed395');
        $this->addSql('ALTER TABLE x_survey.result_geo_object_rating DROP CONSTRAINT result_geo_object_rating_pkey');
        $this->addSql('ALTER TABLE x_survey.result_geo_object_rating DROP id');
        $this->addSql('ALTER TABLE x_survey.result_geo_object_rating ADD PRIMARY KEY (user_id, geo_object_id, criterion_subject_id)');
        $this->addSql('DROP INDEX x_survey.uniq_2c0be4f23edc87a76ed39582127c22');
        $this->addSql('ALTER TABLE x_survey.result_criterion_completion DROP CONSTRAINT result_criterion_completion_pkey');
        $this->addSql('ALTER TABLE x_survey.result_criterion_completion DROP id');
        $this->addSql('ALTER TABLE x_survey.result_criterion_completion ADD PRIMARY KEY (user_id, geo_object_id, subject_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX result_geo_object_rating_pkey');
        $this->addSql('ALTER TABLE x_survey.result_geo_object_rating ADD id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_90a3f11ea62dde3782127c22a76ed395 ON x_survey.result_geo_object_rating (criterion_subject_id, geo_object_id, user_id)');
        $this->addSql('ALTER TABLE x_survey.result_geo_object_rating ADD PRIMARY KEY (id)');
        $this->addSql('DROP INDEX result_criterion_completion_pkey');
        $this->addSql('ALTER TABLE x_survey.result_criterion_completion ADD id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_2c0be4f23edc87a76ed39582127c22 ON x_survey.result_criterion_completion (subject_id, user_id, geo_object_id)');
        $this->addSql('ALTER TABLE x_survey.result_criterion_completion ADD PRIMARY KEY (id)');
    }
}
