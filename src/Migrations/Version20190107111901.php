<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190107111901 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Init migration';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA x_survey');
        $this->addSql('CREATE SCHEMA x_geospatial');
        $this->addSql('CREATE SCHEMA x_geometry');
        $this->addSql('CREATE SCHEMA x_main');
        $this->addSql('CREATE TABLE x_survey.q_answer (id INT GENERATED ALWAYS AS IDENTITY, question_id INT DEFAULT NULL, parent INT DEFAULT NULL, title VARCHAR(255) NOT NULL, is_free_answer BOOLEAN NOT NULL, uuid UUID NOT NULL, position SMALLINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DA5E71E7D17F50A6 ON x_survey.q_answer (uuid)');
        $this->addSql('CREATE INDEX IDX_DA5E71E71E27F6BF ON x_survey.q_answer (question_id)');
        $this->addSql('CREATE INDEX IDX_DA5E71E73D8E604F ON x_survey.q_answer (parent)');
        $this->addSql('CREATE TABLE x_survey.q_question (id INT GENERATED ALWAYS AS IDENTITY, category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, has_multiple_answers BOOLEAN NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FE301525D17F50A6 ON x_survey.q_question (uuid)');
        $this->addSql('CREATE INDEX IDX_FE30152512469DE2 ON x_survey.q_question (category_id)');
        $this->addSql('CREATE TABLE x_survey.q_flow (id INT GENERATED ALWAYS AS IDENTITY, question_id INT DEFAULT NULL, answer_id INT DEFAULT NULL, action VARCHAR(255) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BA871167D17F50A6 ON x_survey.q_flow (uuid)');
        $this->addSql('CREATE INDEX IDX_BA8711671E27F6BF ON x_survey.q_flow (question_id)');
        $this->addSql('CREATE INDEX IDX_BA871167AA334807 ON x_survey.q_flow (answer_id)');
        $this->addSql('CREATE TABLE x_survey.result_geo_object_rating (id INT GENERATED ALWAYS AS IDENTITY, criterion_subject_id INT NOT NULL, geo_object_id INT NOT NULL, user_id INT NOT NULL, rating NUMERIC(4, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_90A3F11EA62DDE37 ON x_survey.result_geo_object_rating (criterion_subject_id)');
        $this->addSql('CREATE INDEX IDX_90A3F11E82127C22 ON x_survey.result_geo_object_rating (geo_object_id)');
        $this->addSql('CREATE INDEX IDX_90A3F11EA76ED395 ON x_survey.result_geo_object_rating (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_90A3F11EA62DDE3782127C22A76ED395 ON x_survey.result_geo_object_rating (criterion_subject_id, geo_object_id, user_id)');
        $this->addSql('CREATE TABLE x_survey.result_user_completion (user_id INT NOT NULL, geo_object_id INT NOT NULL, data JSONB DEFAULT NULL, PRIMARY KEY(user_id, geo_object_id))');
        $this->addSql('CREATE INDEX IDX_3B8A0563A76ED395 ON x_survey.result_user_completion (user_id)');
        $this->addSql('CREATE INDEX IDX_3B8A056382127C22 ON x_survey.result_user_completion (geo_object_id)');
        $this->addSql('COMMENT ON COLUMN x_survey.result_user_completion.data IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE TABLE x_survey.result_criterion_completion (id INT GENERATED ALWAYS AS IDENTITY, subject_id INT NOT NULL, user_id INT NOT NULL, geo_object_id INT NOT NULL, is_complete BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2C0BE4FA76ED395 ON x_survey.result_criterion_completion (user_id)');
        $this->addSql('CREATE INDEX IDX_2C0BE4F82127C22 ON x_survey.result_criterion_completion (geo_object_id)');
        $this->addSql('CREATE INDEX IDX_2C0BE4F23EDC87 ON x_survey.result_criterion_completion (subject_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2C0BE4F23EDC87A76ED39582127C22 ON x_survey.result_criterion_completion (subject_id, user_id, geo_object_id)');
        $this->addSql('CREATE TABLE x_survey.response_answer (id INT GENERATED ALWAYS AS IDENTITY, question_id INT NOT NULL, answer_id INT NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6440E5CED17F50A6 ON x_survey.response_answer (uuid)');
        $this->addSql('CREATE INDEX IDX_6440E5CE1E27F6BF ON x_survey.response_answer (question_id)');
        $this->addSql('CREATE INDEX IDX_6440E5CEAA334807 ON x_survey.response_answer (answer_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6440E5CE1E27F6BFAA334807 ON x_survey.response_answer (question_id, answer_id)');
        $this->addSql('CREATE TABLE x_survey.response_question (id INT GENERATED ALWAYS AS IDENTITY, user_id INT NOT NULL, location_id INT DEFAULT NULL, question_id INT NOT NULL, geo_object_id INT NOT NULL, answered_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_latest BOOLEAN NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4D14638DD17F50A6 ON x_survey.response_question (uuid)');
        $this->addSql('CREATE INDEX IDX_4D14638DA76ED395 ON x_survey.response_question (user_id)');
        $this->addSql('CREATE INDEX IDX_4D14638D64D218E ON x_survey.response_question (location_id)');
        $this->addSql('CREATE INDEX IDX_4D14638D1E27F6BF ON x_survey.response_question (question_id)');
        $this->addSql('CREATE INDEX IDX_4D14638D82127C22 ON x_survey.response_question (geo_object_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4D14638DA76ED3951E27F6BF82127C22 ON x_survey.response_question (user_id, question_id, geo_object_id) WHERE (is_latest IS TRUE)');
        $this->addSql('COMMENT ON COLUMN x_survey.response_question.answered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN x_survey.response_question.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE x_survey.response_location (id INT GENERATED ALWAYS AS IDENTITY, user_id INT NOT NULL, geo_object_id INT NOT NULL, coordinates Geography(Point) DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A57DA308D17F50A6 ON x_survey.response_location (uuid)');
        $this->addSql('CREATE INDEX IDX_A57DA308A76ED395 ON x_survey.response_location (user_id)');
        $this->addSql('CREATE INDEX IDX_A57DA30882127C22 ON x_survey.response_location (geo_object_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A57DA308A76ED39582127C22 ON x_survey.response_location (user_id, geo_object_id) WHERE (coordinates IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A57DA308A76ED39582127C229816D676 ON x_survey.response_location (user_id, geo_object_id, coordinates) WHERE (coordinates IS NOT NULL)');
        $this->addSql('COMMENT ON COLUMN x_survey.response_location.coordinates IS \'(DC2Type:point)\'');
        $this->addSql('COMMENT ON COLUMN x_survey.response_location.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE x_survey.ev_criterion_subject (id INT GENERATED ALWAYS AS IDENTITY, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F8ABFB21D17F50A6 ON x_survey.ev_criterion_subject (uuid)');
        $this->addSql('CREATE INDEX IDX_F8ABFB2112469DE2 ON x_survey.ev_criterion_subject (category_id)');
        $this->addSql('CREATE TABLE x_survey.ev_indicator_subject (id INT GENERATED ALWAYS AS IDENTITY, criterion_id INT NOT NULL, name VARCHAR(255) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52312F6DD17F50A6 ON x_survey.ev_indicator_subject (uuid)');
        $this->addSql('CREATE INDEX IDX_52312F6D97766307 ON x_survey.ev_indicator_subject (criterion_id)');
        $this->addSql('CREATE TABLE x_survey.ev_criterion_definition (id INT GENERATED ALWAYS AS IDENTITY, subject_id INT DEFAULT NULL, answer_id INT NOT NULL, value NUMERIC(2, 1) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_838C5856D17F50A6 ON x_survey.ev_criterion_definition (uuid)');
        $this->addSql('CREATE INDEX IDX_838C585623EDC87 ON x_survey.ev_criterion_definition (subject_id)');
        $this->addSql('CREATE INDEX IDX_838C5856AA334807 ON x_survey.ev_criterion_definition (answer_id)');
        $this->addSql('CREATE TABLE x_survey.ev_indicator_definition (id INT GENERATED ALWAYS AS IDENTITY, indicator_id INT NOT NULL, criterion_id INT NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F825619D17F50A6 ON x_survey.ev_indicator_definition (uuid)');
        $this->addSql('CREATE INDEX IDX_F8256194402854A ON x_survey.ev_indicator_definition (indicator_id)');
        $this->addSql('CREATE INDEX IDX_F82561997766307 ON x_survey.ev_indicator_definition (criterion_id)');
        $this->addSql('CREATE TABLE x_survey.survey (id INT GENERATED ALWAYS AS IDENTITY, name VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AFA3A42AD17F50A6 ON x_survey.survey (uuid)');
        $this->addSql('CREATE TABLE x_survey.survey_scope (survey_id INT NOT NULL, geo_object_id INT NOT NULL, PRIMARY KEY(survey_id, geo_object_id))');
        $this->addSql('CREATE INDEX IDX_24FD0AFFB3FE509D ON x_survey.survey_scope (survey_id)');
        $this->addSql('CREATE INDEX IDX_24FD0AFF82127C22 ON x_survey.survey_scope (geo_object_id)');
        $this->addSql('CREATE TABLE x_survey.survey_element (id INT GENERATED ALWAYS AS IDENTITY, category_id INT NOT NULL, object_type_id INT NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_83A55DA0D17F50A6 ON x_survey.survey_element (uuid)');
        $this->addSql('CREATE INDEX IDX_83A55DA012469DE2 ON x_survey.survey_element (category_id)');
        $this->addSql('CREATE INDEX IDX_83A55DA0C5020C33 ON x_survey.survey_element (object_type_id)');
        $this->addSql('CREATE TABLE x_survey.survey_category (id INT GENERATED ALWAYS AS IDENTITY, parent INT DEFAULT NULL, survey_id INT NOT NULL, name VARCHAR(255) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8F5DD722D17F50A6 ON x_survey.survey_category (uuid)');
        $this->addSql('CREATE INDEX IDX_8F5DD7223D8E604F ON x_survey.survey_category (parent)');
        $this->addSql('CREATE INDEX IDX_8F5DD722B3FE509D ON x_survey.survey_category (survey_id)');
        $this->addSql('CREATE TABLE x_geospatial.geo_object_metadata (geo_object_id INT NOT NULL, has_active_survey BOOLEAN NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(geo_object_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EF7FA8C1D17F50A6 ON x_geospatial.geo_object_metadata (uuid)');
        $this->addSql('CREATE TABLE x_geospatial.geo_object (id INT GENERATED ALWAYS AS IDENTITY, object_type_id INT DEFAULT NULL, attributes JSONB DEFAULT NULL, name VARCHAR(255) NOT NULL, metadata JSONB DEFAULT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_59CD6F4FD17F50A6 ON x_geospatial.geo_object (uuid)');
        $this->addSql('CREATE INDEX IDX_59CD6F4FC5020C33 ON x_geospatial.geo_object (object_type_id)');
        $this->addSql('COMMENT ON COLUMN x_geospatial.geo_object.attributes IS \'(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN x_geospatial.geo_object.metadata IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE TABLE x_geospatial.object_type (id INT GENERATED ALWAYS AS IDENTITY, name VARCHAR(255) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E9F374AED17F50A6 ON x_geospatial.object_type (uuid)');

        $this->addSql('CREATE TABLE x_geometry.geometry_base (id INT GENERATED ALWAYS AS IDENTITY, geo_object_id INT DEFAULT NULL, uuid UUID NOT NULL, coordinates Geography DEFAULT NULL, metadata JSONB DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E7280C8D17F50A6 ON x_geometry.geometry_base (uuid)');
        $this->addSql('CREATE INDEX IDX_4E7280C882127C22 ON x_geometry.geometry_base (geo_object_id)');
        $this->addSql('CREATE INDEX ON x_geometry.geometry_base USING GIST (coordinates)');
        $this->addSql('CREATE INDEX ON x_geometry.geometry_base USING GIST ((coordinates::geometry))');
        $this->addSql('COMMENT ON COLUMN x_geometry.geometry_base.coordinates IS \'(DC2Type:geography)\'');
        $this->addSql('COMMENT ON COLUMN x_geometry.geometry_base.metadata IS \'(DC2Type:json_array)\'');

        $this->addSql('CREATE TABLE x_geometry.point (id INT GENERATED ALWAYS AS IDENTITY, CONSTRAINT geom_point_pk PRIMARY KEY (id)) INHERITS (x_geometry.geometry_base)');
        $this->addSql('CREATE INDEX IDX_E0B01AC982127C22 ON x_geometry.point (geo_object_id)');
        $this->addSql('CREATE INDEX ON x_geometry.point USING GIST (coordinates)');
        $this->addSql('CREATE INDEX ON x_geometry.point USING GIST ((coordinates::geometry))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8D4B651D17F50A2 ON x_geometry.point (uuid)');
        $this->addSql('COMMENT ON COLUMN x_geometry.point.coordinates IS \'(DC2Type:geography)\'');
        $this->addSql('COMMENT ON COLUMN x_geometry.point.metadata IS \'(DC2Type:json_array)\'');

        $this->addSql('CREATE TABLE x_geometry.multiline (id INT GENERATED ALWAYS AS IDENTITY, CONSTRAINT geom_multiline_pk PRIMARY KEY (id)) INHERITS (x_geometry.geometry_base)');
        $this->addSql('CREATE INDEX IDX_57DEAAB082127C22 ON x_geometry.multiline (geo_object_id)');
        $this->addSql('CREATE INDEX ON x_geometry.multiline USING GIST (coordinates)');
        $this->addSql('CREATE INDEX ON x_geometry.multiline USING GIST ((coordinates::geometry))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8D4B651D17F50A3 ON x_geometry.multiline (uuid)');
        $this->addSql('COMMENT ON COLUMN x_geometry.multiline.coordinates IS \'(DC2Type:geography)\'');
        $this->addSql('COMMENT ON COLUMN x_geometry.multiline.metadata IS \'(DC2Type:json_array)\'');

        $this->addSql('CREATE TABLE x_geometry.line (id INT GENERATED ALWAYS AS IDENTITY, CONSTRAINT geom_line_pk PRIMARY KEY (id)) INHERITS (x_geometry.geometry_base)');
        $this->addSql('CREATE INDEX IDX_AC487C8282127C22 ON x_geometry.line (geo_object_id)');
        $this->addSql('CREATE INDEX ON x_geometry.line USING GIST (coordinates)');
        $this->addSql('CREATE INDEX ON x_geometry.line USING GIST ((coordinates::geometry))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8D4B651D17F50A4 ON x_geometry.line (uuid)');
        $this->addSql('COMMENT ON COLUMN x_geometry.line.coordinates IS \'(DC2Type:geography)\'');
        $this->addSql('COMMENT ON COLUMN x_geometry.line.metadata IS \'(DC2Type:json_array)\'');

        $this->addSql('CREATE TABLE x_geometry.polygon (id INT GENERATED ALWAYS AS IDENTITY, CONSTRAINT geom_polygon_pk PRIMARY KEY (id)) INHERITS (x_geometry.geometry_base)');
        $this->addSql('CREATE INDEX IDX_F5A2A17A82127C22 ON x_geometry.polygon (geo_object_id)');
        $this->addSql('CREATE INDEX ON x_geometry.polygon USING GIST (coordinates)');
        $this->addSql('CREATE INDEX ON x_geometry.polygon USING GIST ((coordinates::geometry))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8D4B651D17F50A1 ON x_geometry.polygon (uuid)');
        $this->addSql('COMMENT ON COLUMN x_geometry.polygon.coordinates IS \'(DC2Type:geography)\'');
        $this->addSql('COMMENT ON COLUMN x_geometry.polygon.metadata IS \'(DC2Type:json_array)\'');

        $this->addSql('CREATE TABLE x_main.user_base (id INT GENERATED ALWAYS AS IDENTITY, username VARCHAR(255) NOT NULL, roles VARCHAR(250) NOT NULL, last_login DATE DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C86CC2C7F85E0677 ON x_main.user_base (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C86CC2C7E7927C74 ON x_main.user_base (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C86CC2C7D17F50A6 ON x_main.user_base (uuid)');
        $this->addSql('COMMENT ON COLUMN x_main.user_base.last_login IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE x_survey.q_answer ADD CONSTRAINT FK_DA5E71E71E27F6BF FOREIGN KEY (question_id) REFERENCES x_survey.q_question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.q_answer ADD CONSTRAINT FK_DA5E71E73D8E604F FOREIGN KEY (parent) REFERENCES x_survey.q_answer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.q_question ADD CONSTRAINT FK_FE30152512469DE2 FOREIGN KEY (category_id) REFERENCES x_survey.survey_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.q_flow ADD CONSTRAINT FK_BA8711671E27F6BF FOREIGN KEY (question_id) REFERENCES x_survey.q_question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.q_flow ADD CONSTRAINT FK_BA871167AA334807 FOREIGN KEY (answer_id) REFERENCES x_survey.q_answer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.result_geo_object_rating ADD CONSTRAINT FK_90A3F11EA62DDE37 FOREIGN KEY (criterion_subject_id) REFERENCES x_survey.ev_criterion_subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.result_geo_object_rating ADD CONSTRAINT FK_90A3F11E82127C22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.result_geo_object_rating ADD CONSTRAINT FK_90A3F11EA76ED395 FOREIGN KEY (user_id) REFERENCES x_main.user_base (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.result_user_completion ADD CONSTRAINT FK_3B8A0563A76ED395 FOREIGN KEY (user_id) REFERENCES x_main.user_base (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.result_user_completion ADD CONSTRAINT FK_3B8A056382127C22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.result_criterion_completion ADD CONSTRAINT FK_2C0BE4F23EDC87 FOREIGN KEY (subject_id) REFERENCES x_survey.ev_criterion_subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.result_criterion_completion ADD CONSTRAINT FK_2C0BE4FA76ED395 FOREIGN KEY (user_id) REFERENCES x_main.user_base (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.result_criterion_completion ADD CONSTRAINT FK_2C0BE4F82127C22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.response_answer ADD CONSTRAINT FK_6440E5CE1E27F6BF FOREIGN KEY (question_id) REFERENCES x_survey.response_question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.response_answer ADD CONSTRAINT FK_6440E5CEAA334807 FOREIGN KEY (answer_id) REFERENCES x_survey.q_answer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.response_question ADD CONSTRAINT FK_4D14638DA76ED395 FOREIGN KEY (user_id) REFERENCES x_main.user_base (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.response_question ADD CONSTRAINT FK_4D14638D64D218E FOREIGN KEY (location_id) REFERENCES x_survey.response_location (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.response_question ADD CONSTRAINT FK_4D14638D1E27F6BF FOREIGN KEY (question_id) REFERENCES x_survey.q_question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.response_question ADD CONSTRAINT FK_4D14638D82127C22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.response_location ADD CONSTRAINT FK_A57DA308A76ED395 FOREIGN KEY (user_id) REFERENCES x_main.user_base (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.response_location ADD CONSTRAINT FK_A57DA30882127C22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.ev_criterion_subject ADD CONSTRAINT FK_F8ABFB2112469DE2 FOREIGN KEY (category_id) REFERENCES x_survey.survey_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.ev_indicator_subject ADD CONSTRAINT FK_52312F6D97766307 FOREIGN KEY (criterion_id) REFERENCES x_survey.ev_criterion_subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.ev_criterion_definition ADD CONSTRAINT FK_838C585623EDC87 FOREIGN KEY (subject_id) REFERENCES x_survey.ev_criterion_subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.ev_criterion_definition ADD CONSTRAINT FK_838C5856AA334807 FOREIGN KEY (answer_id) REFERENCES x_survey.q_answer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.ev_indicator_definition ADD CONSTRAINT FK_F8256194402854A FOREIGN KEY (indicator_id) REFERENCES x_survey.ev_criterion_subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.ev_indicator_definition ADD CONSTRAINT FK_F82561997766307 FOREIGN KEY (criterion_id) REFERENCES x_survey.ev_criterion_definition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.survey_scope ADD CONSTRAINT FK_24FD0AFFB3FE509D FOREIGN KEY (survey_id) REFERENCES x_survey.survey (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.survey_scope ADD CONSTRAINT FK_24FD0AFF82127C22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.survey_element ADD CONSTRAINT FK_83A55DA012469DE2 FOREIGN KEY (category_id) REFERENCES x_survey.survey_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.survey_element ADD CONSTRAINT FK_83A55DA0C5020C33 FOREIGN KEY (object_type_id) REFERENCES x_geospatial.object_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.survey_category ADD CONSTRAINT FK_8F5DD7223D8E604F FOREIGN KEY (parent) REFERENCES x_survey.survey_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.survey_category ADD CONSTRAINT FK_8F5DD722B3FE509D FOREIGN KEY (survey_id) REFERENCES x_survey.survey (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geospatial.geo_object_metadata ADD CONSTRAINT FK_EF7FA8C182127C22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geospatial.geo_object ADD CONSTRAINT FK_59CD6F4FC5020C33 FOREIGN KEY (object_type_id) REFERENCES x_geospatial.object_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geometry.point ADD CONSTRAINT FK_E0B01AC982127C22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geometry.multiline ADD CONSTRAINT FK_57DEAAB082127C22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geometry.geometry_base ADD CONSTRAINT FK_4E7280C882127C22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geometry.line ADD CONSTRAINT FK_AC487C8282127C22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geometry.polygon ADD CONSTRAINT FK_F5A2A17A82127C22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE x_survey.q_answer DROP CONSTRAINT FK_DA5E71E73D8E604F');
        $this->addSql('ALTER TABLE x_survey.q_flow DROP CONSTRAINT FK_BA871167AA334807');
        $this->addSql('ALTER TABLE x_survey.response_answer DROP CONSTRAINT FK_6440E5CEAA334807');
        $this->addSql('ALTER TABLE x_survey.ev_criterion_definition DROP CONSTRAINT FK_838C5856AA334807');
        $this->addSql('ALTER TABLE x_survey.q_answer DROP CONSTRAINT FK_DA5E71E71E27F6BF');
        $this->addSql('ALTER TABLE x_survey.q_flow DROP CONSTRAINT FK_BA8711671E27F6BF');
        $this->addSql('ALTER TABLE x_survey.response_question DROP CONSTRAINT FK_4D14638D1E27F6BF');
        $this->addSql('ALTER TABLE x_survey.response_answer DROP CONSTRAINT FK_6440E5CE1E27F6BF');
        $this->addSql('ALTER TABLE x_survey.response_question DROP CONSTRAINT FK_4D14638D64D218E');
        $this->addSql('ALTER TABLE x_survey.result_geo_object_rating DROP CONSTRAINT FK_90A3F11EA62DDE37');
        $this->addSql('ALTER TABLE x_survey.result_criterion_completion DROP CONSTRAINT FK_2C0BE4F23EDC87');
        $this->addSql('ALTER TABLE x_survey.ev_indicator_subject DROP CONSTRAINT FK_52312F6D97766307');
        $this->addSql('ALTER TABLE x_survey.ev_criterion_definition DROP CONSTRAINT FK_838C585623EDC87');
        $this->addSql('ALTER TABLE x_survey.ev_indicator_definition DROP CONSTRAINT FK_F8256194402854A');
        $this->addSql('ALTER TABLE x_survey.ev_indicator_definition DROP CONSTRAINT FK_F82561997766307');
        $this->addSql('ALTER TABLE x_survey.survey_scope DROP CONSTRAINT FK_24FD0AFFB3FE509D');
        $this->addSql('ALTER TABLE x_survey.survey_category DROP CONSTRAINT FK_8F5DD722B3FE509D');
        $this->addSql('ALTER TABLE x_survey.q_question DROP CONSTRAINT FK_FE30152512469DE2');
        $this->addSql('ALTER TABLE x_survey.ev_criterion_subject DROP CONSTRAINT FK_F8ABFB2112469DE2');
        $this->addSql('ALTER TABLE x_survey.survey_element DROP CONSTRAINT FK_83A55DA012469DE2');
        $this->addSql('ALTER TABLE x_survey.survey_category DROP CONSTRAINT FK_8F5DD7223D8E604F');
        $this->addSql('ALTER TABLE x_survey.result_geo_object_rating DROP CONSTRAINT FK_90A3F11E82127C22');
        $this->addSql('ALTER TABLE x_survey.result_user_completion DROP CONSTRAINT FK_3B8A056382127C22');
        $this->addSql('ALTER TABLE x_survey.result_criterion_completion DROP CONSTRAINT FK_2C0BE4F82127C22');
        $this->addSql('ALTER TABLE x_survey.response_question DROP CONSTRAINT FK_4D14638D82127C22');
        $this->addSql('ALTER TABLE x_survey.response_location DROP CONSTRAINT FK_A57DA30882127C22');
        $this->addSql('ALTER TABLE x_survey.survey_scope DROP CONSTRAINT FK_24FD0AFF82127C22');
        $this->addSql('ALTER TABLE x_geospatial.geo_object_metadata DROP CONSTRAINT FK_EF7FA8C182127C22');
        $this->addSql('ALTER TABLE x_geometry.point DROP CONSTRAINT FK_E0B01AC982127C22');
        $this->addSql('ALTER TABLE x_geometry.multiline DROP CONSTRAINT FK_57DEAAB082127C22');
        $this->addSql('ALTER TABLE x_geometry.geometry_base DROP CONSTRAINT FK_4E7280C882127C22');
        $this->addSql('ALTER TABLE x_geometry.line DROP CONSTRAINT FK_AC487C8282127C22');
        $this->addSql('ALTER TABLE x_geometry.polygon DROP CONSTRAINT FK_F5A2A17A82127C22');
        $this->addSql('ALTER TABLE x_survey.survey_element DROP CONSTRAINT FK_83A55DA0C5020C33');
        $this->addSql('ALTER TABLE x_geospatial.geo_object DROP CONSTRAINT FK_59CD6F4FC5020C33');
        $this->addSql('ALTER TABLE x_survey.result_geo_object_rating DROP CONSTRAINT FK_90A3F11EA76ED395');
        $this->addSql('ALTER TABLE x_survey.result_user_completion DROP CONSTRAINT FK_3B8A0563A76ED395');
        $this->addSql('ALTER TABLE x_survey.result_criterion_completion DROP CONSTRAINT FK_2C0BE4FA76ED395');
        $this->addSql('ALTER TABLE x_survey.response_question DROP CONSTRAINT FK_4D14638DA76ED395');
        $this->addSql('ALTER TABLE x_survey.response_location DROP CONSTRAINT FK_A57DA308A76ED395');
        $this->addSql('DROP TABLE x_survey.q_answer');
        $this->addSql('DROP TABLE x_survey.q_question');
        $this->addSql('DROP TABLE x_survey.q_flow');
        $this->addSql('DROP TABLE x_survey.result_geo_object_rating');
        $this->addSql('DROP TABLE x_survey.result_user_completion');
        $this->addSql('DROP TABLE x_survey.result_criterion_completion');
        $this->addSql('DROP TABLE x_survey.response_answer');
        $this->addSql('DROP TABLE x_survey.response_question');
        $this->addSql('DROP TABLE x_survey.response_location');
        $this->addSql('DROP TABLE x_survey.ev_criterion_subject');
        $this->addSql('DROP TABLE x_survey.ev_indicator_subject');
        $this->addSql('DROP TABLE x_survey.ev_criterion_definition');
        $this->addSql('DROP TABLE x_survey.ev_indicator_definition');
        $this->addSql('DROP TABLE x_survey.survey');
        $this->addSql('DROP TABLE x_survey.survey_scope');
        $this->addSql('DROP TABLE x_survey.survey_element');
        $this->addSql('DROP TABLE x_survey.survey_category');
        $this->addSql('DROP TABLE x_geospatial.geo_object_metadata');
        $this->addSql('DROP TABLE x_geospatial.geo_object');
        $this->addSql('DROP TABLE x_geospatial.object_type');
        $this->addSql('DROP TABLE x_geometry.point');
        $this->addSql('DROP TABLE x_geometry.multiline');
        $this->addSql('DROP TABLE x_geometry.geometry_base');
        $this->addSql('DROP TABLE x_geometry.line');
        $this->addSql('DROP TABLE x_geometry.polygon');
        $this->addSql('DROP TABLE x_main.user_base');
    }
}
