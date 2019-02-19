<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190205110126 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE x_survey.survey_auxiliary_object_type (id INT GENERATED ALWAYS AS IDENTITY, survey_id INT DEFAULT NULL, object_type_id INT NOT NULL, behavior VARCHAR(255) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1377547ED17F50A6 ON x_survey.survey_auxiliary_object_type (uuid)');
        $this->addSql('CREATE INDEX IDX_1377547EB3FE509D ON x_survey.survey_auxiliary_object_type (survey_id)');
        $this->addSql('CREATE INDEX IDX_1377547EC5020C33 ON x_survey.survey_auxiliary_object_type (object_type_id)');
        $this->addSql('ALTER TABLE x_survey.survey_auxiliary_object_type ADD CONSTRAINT FK_1377547EB3FE509D FOREIGN KEY (survey_id) REFERENCES x_survey.survey (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.survey_auxiliary_object_type ADD CONSTRAINT FK_1377547EC5020C33 FOREIGN KEY (object_type_id) REFERENCES x_geospatial.object_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE x_survey.survey_auxiliary_object_type');
    }
}
