<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190403140405 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE IF EXISTS x_survey.result_user_completion');
        $this->addSql('CREATE TABLE x_survey.result_user_completion (user_id INT NOT NULL, geo_object_id INT NOT NULL, survey_id INT NOT NULL, is_completed BOOLEAN NOT NULL, PRIMARY KEY(user_id, geo_object_id, survey_id))');
        $this->addSql('CREATE INDEX IDX_3B8A0563A76ED395 ON x_survey.result_user_completion (user_id)');
        $this->addSql('CREATE INDEX IDX_3B8A056382127C22 ON x_survey.result_user_completion (geo_object_id)');
        $this->addSql('CREATE INDEX IDX_3B8A0563B3FE509D ON x_survey.result_user_completion (survey_id)');
        $this->addSql('ALTER TABLE x_survey.result_user_completion ADD CONSTRAINT FK_3B8A0563A76ED395 FOREIGN KEY (user_id) REFERENCES x_main.user_base (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.result_user_completion ADD CONSTRAINT FK_3B8A056382127C22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.result_user_completion ADD CONSTRAINT FK_3B8A0563B3FE509D FOREIGN KEY (survey_id) REFERENCES x_survey.survey (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE x_survey.result_user_completion');
    }
}
