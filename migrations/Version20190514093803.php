<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190514093803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE x_survey.spatial_geo_object (geo_object_id INT NOT NULL, survey_id INT NOT NULL, properties JSONB DEFAULT \'{}\' NOT NULL, geo_object_name VARCHAR(255) NOT NULL, uuid UUID NOT NULL, object_type_id INT NOT NULL, object_type_name VARCHAR(255) NOT NULL, metadata JSONB DEFAULT \'{}\' NOT NULL, base_style VARCHAR(255) DEFAULT NULL, hover_style VARCHAR(255) DEFAULT NULL, PRIMARY KEY(geo_object_id, survey_id))');
        $this->addSql('CREATE INDEX IDX_C0DB01B782127C22 ON x_survey.spatial_geo_object (geo_object_id)');
        $this->addSql('CREATE INDEX IDX_C0DB01B7B3FE509D ON x_survey.spatial_geo_object (survey_id)');
        $this->addSql('COMMENT ON COLUMN x_survey.spatial_geo_object.properties IS \'(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN x_survey.spatial_geo_object.metadata IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE TABLE x_survey.spatial_scope (survey_id INT NOT NULL, geo_object_id INT NOT NULL, PRIMARY KEY(survey_id, geo_object_id))');
        $this->addSql('CREATE INDEX IDX_C3C8E451B3FE509D ON x_survey.spatial_scope (survey_id)');
        $this->addSql('CREATE INDEX IDX_C3C8E45182127C22 ON x_survey.spatial_scope (geo_object_id)');
        $this->addSql('ALTER TABLE x_survey.spatial_geo_object ADD CONSTRAINT FK_C0DB01B782127C22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.spatial_geo_object ADD CONSTRAINT FK_C0DB01B7B3FE509D FOREIGN KEY (survey_id) REFERENCES x_survey.survey (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.spatial_scope ADD CONSTRAINT FK_C3C8E451B3FE509D FOREIGN KEY (survey_id) REFERENCES x_survey.survey (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.spatial_scope ADD CONSTRAINT FK_C3C8E45182127C22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE x_survey.survey_scope');
        $this->addSql('ALTER TABLE x_geospatial.geo_object ADD properties JSONB DEFAULT \'{}\' NOT NULL');
        $this->addSql('ALTER TABLE x_geospatial.geo_object ADD local_properties JSONB DEFAULT \'{}\' NOT NULL');
        $this->addSql('ALTER TABLE x_geospatial.geo_object DROP attributes');
        $this->addSql('ALTER TABLE x_geospatial.geo_object DROP style_base');
        $this->addSql('ALTER TABLE x_geospatial.geo_object DROP style_hover');
        $this->addSql('COMMENT ON COLUMN x_geospatial.geo_object.properties IS \'(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN x_geospatial.geo_object.local_properties IS \'(DC2Type:json_array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE x_survey.survey_scope (survey_id INT NOT NULL, geo_object_id INT NOT NULL, PRIMARY KEY(survey_id, geo_object_id))');
        $this->addSql('CREATE INDEX idx_24fd0affb3fe509d ON x_survey.survey_scope (survey_id)');
        $this->addSql('CREATE INDEX idx_24fd0aff82127c22 ON x_survey.survey_scope (geo_object_id)');
        $this->addSql('ALTER TABLE x_survey.survey_scope ADD CONSTRAINT fk_24fd0aff82127c22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.survey_scope ADD CONSTRAINT fk_24fd0affb3fe509d FOREIGN KEY (survey_id) REFERENCES x_survey.survey (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE x_survey.spatial_geo_object');
        $this->addSql('DROP TABLE x_survey.spatial_scope');
        $this->addSql('ALTER TABLE x_geospatial.geo_object ADD attributes JSONB DEFAULT NULL');
        $this->addSql('ALTER TABLE x_geospatial.geo_object ADD style_base VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE x_geospatial.geo_object ADD style_hover VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE x_geospatial.geo_object DROP properties');
        $this->addSql('ALTER TABLE x_geospatial.geo_object DROP local_properties');
        $this->addSql('COMMENT ON COLUMN x_geospatial.geo_object.attributes IS \'(DC2Type:json_array)\'');
    }
}
