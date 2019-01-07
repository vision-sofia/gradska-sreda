<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190107111901 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA x_geospatial');
        $this->addSql('CREATE SCHEMA x_geometry');
        $this->addSql('CREATE SCHEMA x_main');
        $this->addSql('CREATE SCHEMA x_survey');
        $this->addSql('CREATE TABLE x_geospatial.label (id INT GENERATED ALWAYS AS IDENTITY, name VARCHAR(255) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42C7004CD17F50A6 ON x_geospatial.label (uuid)');
        $this->addSql('CREATE TABLE x_geospatial.layer (id INT GENERATED ALWAYS AS IDENTITY, name VARCHAR(255) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A8BB71BED17F50A6 ON x_geospatial.layer (uuid)');
        $this->addSql('CREATE TABLE x_geospatial.geospatial_object (id INT GENERATED ALWAYS AS IDENTITY, layer_id INT DEFAULT NULL, attributes JSONB DEFAULT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D993DD83D17F50A6 ON x_geospatial.geospatial_object (uuid)');
        $this->addSql('CREATE INDEX IDX_D993DD83EA6EFDCD ON x_geospatial.geospatial_object (layer_id)');
        $this->addSql('COMMENT ON COLUMN x_geospatial.geospatial_object.attributes IS \'(DC2Type:json_array)\'');

        $this->addSql('CREATE TABLE x_geometry.geometry_base (id INT GENERATED ALWAYS AS IDENTITY, layer_id INT DEFAULT NULL, spatial_object_id INT DEFAULT NULL, uuid UUID NOT NULL, coordinates Geography DEFAULT NULL, metadata JSONB DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E7280C8D17F50A6 ON x_geometry.geometry_base (uuid)');
        $this->addSql('CREATE INDEX IDX_4E7280C8EA6EFDCD ON x_geometry.geometry_base (layer_id)');
        $this->addSql('CREATE INDEX IDX_4E7280C8C4C51E68 ON x_geometry.geometry_base (spatial_object_id)');
        $this->addSql('COMMENT ON COLUMN x_geometry.geometry_base.coordinates IS \'(DC2Type:geography)\'');
        $this->addSql('COMMENT ON COLUMN x_geometry.geometry_base.metadata IS \'(DC2Type:json_array)\'');

        $this->addSql('CREATE TABLE x_geometry.point (CONSTRAINT geom_point_pk PRIMARY KEY (id)) INHERITS (x_geometry.geometry_base)');
        $this->addSql('CREATE INDEX IDX_E0B01AC9EA6EFDCD ON x_geometry.point (layer_id)');
        $this->addSql('CREATE INDEX IDX_E0B01AC9C4C51E68 ON x_geometry.point (spatial_object_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8D4B651D17F50A2 ON x_geometry.point (uuid)');
        $this->addSql('COMMENT ON COLUMN x_geometry.point.coordinates IS \'(DC2Type:geography)\'');
        $this->addSql('COMMENT ON COLUMN x_geometry.point.metadata IS \'(DC2Type:json_array)\'');

        $this->addSql('CREATE TABLE x_geometry.multiline (CONSTRAINT geom_multiline_pk PRIMARY KEY (id)) INHERITS (x_geometry.geometry_base)');
        $this->addSql('CREATE INDEX IDX_57DEAAB0EA6EFDCD ON x_geometry.multiline (layer_id)');
        $this->addSql('CREATE INDEX IDX_57DEAAB0C4C51E68 ON x_geometry.multiline (spatial_object_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8D4B651D17F50A3 ON x_geometry.multiline (uuid)');
        $this->addSql('COMMENT ON COLUMN x_geometry.multiline.coordinates IS \'(DC2Type:geography)\'');
        $this->addSql('COMMENT ON COLUMN x_geometry.multiline.metadata IS \'(DC2Type:json_array)\'');

        $this->addSql('CREATE TABLE x_geometry.line (CONSTRAINT geom_line_pk PRIMARY KEY (id)) INHERITS (x_geometry.geometry_base)');
        $this->addSql('CREATE INDEX IDX_AC487C82EA6EFDCD ON x_geometry.line (layer_id)');
        $this->addSql('CREATE INDEX IDX_AC487C82C4C51E68 ON x_geometry.line (spatial_object_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8D4B651D17F50A4 ON x_geometry.line (uuid)');
        $this->addSql('COMMENT ON COLUMN x_geometry.line.coordinates IS \'(DC2Type:geography)\'');
        $this->addSql('COMMENT ON COLUMN x_geometry.line.metadata IS \'(DC2Type:json_array)\'');

        $this->addSql('CREATE TABLE x_geometry.polygon (CONSTRAINT geom_polygon_pk PRIMARY KEY (id)) INHERITS (x_geometry.geometry_base)');
        $this->addSql('CREATE INDEX IDX_F5A2A17AEA6EFDCD ON x_geometry.polygon (layer_id)');
        $this->addSql('CREATE INDEX IDX_F5A2A17AC4C51E68 ON x_geometry.polygon (spatial_object_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8D4B651D17F50A1 ON x_geometry.polygon (uuid)');
        $this->addSql('COMMENT ON COLUMN x_geometry.polygon.coordinates IS \'(DC2Type:geography)\'');
        $this->addSql('COMMENT ON COLUMN x_geometry.polygon.metadata IS \'(DC2Type:json_array)\'');

        $this->addSql('CREATE TABLE x_main.user_base (id INT GENERATED ALWAYS AS IDENTITY, username VARCHAR(255) NOT NULL, roles VARCHAR(250) NOT NULL, last_login DATE DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C86CC2C7F85E0677 ON x_main.user_base (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C86CC2C7E7927C74 ON x_main.user_base (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C86CC2C7D17F50A6 ON x_main.user_base (uuid)');
        $this->addSql('COMMENT ON COLUMN x_main.user_base.last_login IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE x_survey.question_answer (id INT GENERATED ALWAYS AS IDENTITY, question_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, is_free_answer BOOLEAN NOT NULL, uuid UUID NOT NULL, position SMALLINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_866EDE9D17F50A6 ON x_survey.question_answer (uuid)');
        $this->addSql('CREATE INDEX IDX_866EDE91E27F6BF ON x_survey.question_answer (question_id)');
        $this->addSql('CREATE INDEX IDX_866EDE9727ACA70 ON x_survey.question_answer (parent_id)');
        $this->addSql('CREATE TABLE x_survey.question (id INT GENERATED ALWAYS AS IDENTITY, title VARCHAR(255) NOT NULL, has_multiple_answers BOOLEAN NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D7F30416D17F50A6 ON x_survey.question (uuid)');
        $this->addSql('CREATE TABLE x_survey.ed_criterion (id INT GENERATED ALWAYS AS IDENTITY, survey_id INT NOT NULL, name VARCHAR(255) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D19F7EC3D17F50A6 ON x_survey.ed_criterion (uuid)');
        $this->addSql('CREATE INDEX IDX_D19F7EC3B3FE509D ON x_survey.ed_criterion (survey_id)');
        $this->addSql('CREATE TABLE x_survey.ed_indicator (id INT GENERATED ALWAYS AS IDENTITY, criterion_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7C29C101D17F50A6 ON x_survey.ed_indicator (uuid)');
        $this->addSql('CREATE INDEX IDX_7C29C10197766307 ON x_survey.ed_indicator (criterion_id)');
        $this->addSql('CREATE TABLE x_survey.ed_point (id INT GENERATED ALWAYS AS IDENTITY, answer_id INT NOT NULL, indicator_id INT NOT NULL, value NUMERIC(2, 1) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6F8BEA03D17F50A6 ON x_survey.ed_point (uuid)');
        $this->addSql('CREATE INDEX IDX_6F8BEA03AA334807 ON x_survey.ed_point (answer_id)');
        $this->addSql('CREATE INDEX IDX_6F8BEA034402854A ON x_survey.ed_point (indicator_id)');
        $this->addSql('CREATE TABLE x_survey.response (id INT GENERATED ALWAYS AS IDENTITY, user_id INT DEFAULT NULL, answer_id INT NOT NULL, survey_id INT NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5F7F46A3D17F50A6 ON x_survey.response (uuid)');
        $this->addSql('CREATE INDEX IDX_5F7F46A3A76ED395 ON x_survey.response (user_id)');
        $this->addSql('CREATE INDEX IDX_5F7F46A3AA334807 ON x_survey.response (answer_id)');
        $this->addSql('CREATE INDEX IDX_5F7F46A3B3FE509D ON x_survey.response (survey_id)');
        $this->addSql('CREATE TABLE x_survey.survey (id INT GENERATED ALWAYS AS IDENTITY, name VARCHAR(255) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AFA3A42AD17F50A6 ON x_survey.survey (uuid)');
        $this->addSql('CREATE TABLE x_survey.survey_subject (id INT GENERATED ALWAYS AS IDENTITY, survey_id INT NOT NULL, question_id INT NOT NULL, sort SMALLINT DEFAULT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_392B3DE3D17F50A6 ON x_survey.survey_subject (uuid)');
        $this->addSql('CREATE INDEX IDX_392B3DE3B3FE509D ON x_survey.survey_subject (survey_id)');
        $this->addSql('CREATE INDEX IDX_392B3DE31E27F6BF ON x_survey.survey_subject (question_id)');
        $this->addSql('ALTER TABLE x_geospatial.geospatial_object ADD CONSTRAINT FK_D993DD83EA6EFDCD FOREIGN KEY (layer_id) REFERENCES x_geospatial.layer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geometry.point ADD CONSTRAINT FK_E0B01AC9EA6EFDCD FOREIGN KEY (layer_id) REFERENCES x_geospatial.layer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geometry.point ADD CONSTRAINT FK_E0B01AC9C4C51E68 FOREIGN KEY (spatial_object_id) REFERENCES x_geospatial.geospatial_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geometry.multiline ADD CONSTRAINT FK_57DEAAB0EA6EFDCD FOREIGN KEY (layer_id) REFERENCES x_geospatial.layer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geometry.multiline ADD CONSTRAINT FK_57DEAAB0C4C51E68 FOREIGN KEY (spatial_object_id) REFERENCES x_geospatial.geospatial_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geometry.geometry_base ADD CONSTRAINT FK_4E7280C8EA6EFDCD FOREIGN KEY (layer_id) REFERENCES x_geospatial.layer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geometry.geometry_base ADD CONSTRAINT FK_4E7280C8C4C51E68 FOREIGN KEY (spatial_object_id) REFERENCES x_geospatial.geospatial_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geometry.line ADD CONSTRAINT FK_AC487C82EA6EFDCD FOREIGN KEY (layer_id) REFERENCES x_geospatial.layer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geometry.line ADD CONSTRAINT FK_AC487C82C4C51E68 FOREIGN KEY (spatial_object_id) REFERENCES x_geospatial.geospatial_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geometry.polygon ADD CONSTRAINT FK_F5A2A17AEA6EFDCD FOREIGN KEY (layer_id) REFERENCES x_geospatial.layer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geometry.polygon ADD CONSTRAINT FK_F5A2A17AC4C51E68 FOREIGN KEY (spatial_object_id) REFERENCES x_geospatial.geospatial_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.question_answer ADD CONSTRAINT FK_866EDE91E27F6BF FOREIGN KEY (question_id) REFERENCES x_survey.question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.question_answer ADD CONSTRAINT FK_866EDE9727ACA70 FOREIGN KEY (parent_id) REFERENCES x_survey.question_answer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.ed_criterion ADD CONSTRAINT FK_D19F7EC3B3FE509D FOREIGN KEY (survey_id) REFERENCES x_survey.survey (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.ed_indicator ADD CONSTRAINT FK_7C29C10197766307 FOREIGN KEY (criterion_id) REFERENCES x_survey.ed_criterion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.ed_point ADD CONSTRAINT FK_6F8BEA03AA334807 FOREIGN KEY (answer_id) REFERENCES x_survey.question_answer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.ed_point ADD CONSTRAINT FK_6F8BEA034402854A FOREIGN KEY (indicator_id) REFERENCES x_survey.ed_indicator (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.response ADD CONSTRAINT FK_5F7F46A3A76ED395 FOREIGN KEY (user_id) REFERENCES x_main.user_base (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.response ADD CONSTRAINT FK_5F7F46A3AA334807 FOREIGN KEY (answer_id) REFERENCES x_survey.question_answer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.response ADD CONSTRAINT FK_5F7F46A3B3FE509D FOREIGN KEY (survey_id) REFERENCES x_survey.survey (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.survey_subject ADD CONSTRAINT FK_392B3DE3B3FE509D FOREIGN KEY (survey_id) REFERENCES x_survey.survey (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.survey_subject ADD CONSTRAINT FK_392B3DE31E27F6BF FOREIGN KEY (question_id) REFERENCES x_survey.question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE x_geospatial.geospatial_object DROP CONSTRAINT FK_D993DD83EA6EFDCD');
        $this->addSql('ALTER TABLE x_geometry.point DROP CONSTRAINT FK_E0B01AC9EA6EFDCD');
        $this->addSql('ALTER TABLE x_geometry.multiline DROP CONSTRAINT FK_57DEAAB0EA6EFDCD');
        $this->addSql('ALTER TABLE x_geometry.geometry_base DROP CONSTRAINT FK_4E7280C8EA6EFDCD');
        $this->addSql('ALTER TABLE x_geometry.line DROP CONSTRAINT FK_AC487C82EA6EFDCD');
        $this->addSql('ALTER TABLE x_geometry.polygon DROP CONSTRAINT FK_F5A2A17AEA6EFDCD');
        $this->addSql('ALTER TABLE x_geometry.point DROP CONSTRAINT FK_E0B01AC9C4C51E68');
        $this->addSql('ALTER TABLE x_geometry.multiline DROP CONSTRAINT FK_57DEAAB0C4C51E68');
        $this->addSql('ALTER TABLE x_geometry.geometry_base DROP CONSTRAINT FK_4E7280C8C4C51E68');
        $this->addSql('ALTER TABLE x_geometry.line DROP CONSTRAINT FK_AC487C82C4C51E68');
        $this->addSql('ALTER TABLE x_geometry.polygon DROP CONSTRAINT FK_F5A2A17AC4C51E68');
        $this->addSql('ALTER TABLE x_survey.response DROP CONSTRAINT FK_5F7F46A3A76ED395');
        $this->addSql('ALTER TABLE x_survey.question_answer DROP CONSTRAINT FK_866EDE9727ACA70');
        $this->addSql('ALTER TABLE x_survey.ed_point DROP CONSTRAINT FK_6F8BEA03AA334807');
        $this->addSql('ALTER TABLE x_survey.response DROP CONSTRAINT FK_5F7F46A3AA334807');
        $this->addSql('ALTER TABLE x_survey.question_answer DROP CONSTRAINT FK_866EDE91E27F6BF');
        $this->addSql('ALTER TABLE x_survey.survey_subject DROP CONSTRAINT FK_392B3DE31E27F6BF');
        $this->addSql('ALTER TABLE x_survey.ed_indicator DROP CONSTRAINT FK_7C29C10197766307');
        $this->addSql('ALTER TABLE x_survey.ed_point DROP CONSTRAINT FK_6F8BEA034402854A');
        $this->addSql('ALTER TABLE x_survey.ed_criterion DROP CONSTRAINT FK_D19F7EC3B3FE509D');
        $this->addSql('ALTER TABLE x_survey.response DROP CONSTRAINT FK_5F7F46A3B3FE509D');
        $this->addSql('ALTER TABLE x_survey.survey_subject DROP CONSTRAINT FK_392B3DE3B3FE509D');
        $this->addSql('DROP TABLE x_geospatial.label');
        $this->addSql('DROP TABLE x_geospatial.layer');
        $this->addSql('DROP TABLE x_geospatial.geospatial_object');
        $this->addSql('DROP TABLE x_geometry.point');
        $this->addSql('DROP TABLE x_geometry.multiline');
        $this->addSql('DROP TABLE x_geometry.geometry_base');
        $this->addSql('DROP TABLE x_geometry.line');
        $this->addSql('DROP TABLE x_geometry.polygon');
        $this->addSql('DROP TABLE x_main.user_base');
        $this->addSql('DROP TABLE x_survey.question_answer');
        $this->addSql('DROP TABLE x_survey.question');
        $this->addSql('DROP TABLE x_survey.ed_criterion');
        $this->addSql('DROP TABLE x_survey.ed_indicator');
        $this->addSql('DROP TABLE x_survey.ed_point');
        $this->addSql('DROP TABLE x_survey.response');
        $this->addSql('DROP TABLE x_survey.survey');
        $this->addSql('DROP TABLE x_survey.survey_subject');
    }
}
