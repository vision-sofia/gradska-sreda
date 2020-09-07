<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200603192007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE x_survey.spatial_geo_object ADD zoom integer[] DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN x_survey.spatial_geo_object.properties IS NULL');
        $this->addSql('COMMENT ON COLUMN x_survey.spatial_geo_object.metadata IS NULL');
        $this->addSql('CREATE INDEX IDX_C0DB01B7B72B7974 ON x_survey.spatial_geo_object USING GIN (zoom)');
        $this->addSql('ALTER TABLE x_survey.response_location ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE x_survey.response_location ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN x_survey.response_location.updated_at IS NULL');
        $this->addSql('ALTER TABLE x_survey.response_question ALTER answered_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE x_survey.response_question ALTER answered_at DROP DEFAULT');
        $this->addSql('ALTER TABLE x_survey.response_question ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE x_survey.response_question ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN x_survey.response_question.answered_at IS NULL');
        $this->addSql('COMMENT ON COLUMN x_survey.response_question.updated_at IS NULL');
        $this->addSql('COMMENT ON COLUMN x_survey.ev_criterion_subject.metadata IS NULL');
        $this->addSql('ALTER TABLE x_main.user_base ALTER last_login TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE x_main.user_base ALTER last_login DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN x_main.user_base.last_login IS NULL');
        $this->addSql('COMMENT ON COLUMN x_geospatial.style_group.style IS NULL');
        $this->addSql('COMMENT ON COLUMN x_geospatial.geo_object.metadata IS NULL');
        $this->addSql('COMMENT ON COLUMN x_geospatial.geo_object.properties IS NULL');
        $this->addSql('COMMENT ON COLUMN x_geospatial.geo_object.local_properties IS NULL');
        $this->addSql('COMMENT ON COLUMN x_geospatial.style_condition.base_style IS NULL');
        $this->addSql('COMMENT ON COLUMN x_geospatial.style_condition.hover_style IS NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE x_main.user_base ALTER last_login TYPE DATE');
        $this->addSql('ALTER TABLE x_main.user_base ALTER last_login DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN x_main.user_base.last_login IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE x_survey.response_location ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE x_survey.response_location ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN x_survey.response_location.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN x_survey.ev_criterion_subject.metadata IS \'(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN x_geospatial.style_condition.base_style IS \'(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN x_geospatial.style_condition.hover_style IS \'(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN x_geospatial.style_group.style IS \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE x_survey.response_question ALTER answered_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE x_survey.response_question ALTER answered_at DROP DEFAULT');
        $this->addSql('ALTER TABLE x_survey.response_question ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE x_survey.response_question ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN x_survey.response_question.answered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN x_survey.response_question.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP INDEX IDX_C0DB01B7B72B7974');
        $this->addSql('ALTER TABLE x_survey.spatial_geo_object DROP zoom');
        $this->addSql('COMMENT ON COLUMN x_survey.spatial_geo_object.properties IS \'(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN x_survey.spatial_geo_object.metadata IS \'(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN x_geospatial.geo_object.properties IS \'(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN x_geospatial.geo_object.local_properties IS \'(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN x_geospatial.geo_object.metadata IS \'(DC2Type:json_array)\'');
    }
}
