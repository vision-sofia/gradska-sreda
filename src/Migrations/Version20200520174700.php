<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200520174700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE x_main.a_survey_completion_achievement (id INT GENERATED ALWAYS AS IDENTITY, survey_category_id INT NOT NULL, zone_id INT DEFAULT NULL, uuid UUID NOT NULL, threshold SMALLINT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_225BC2BFD17F50A6 ON x_main.a_survey_completion_achievement (uuid)');
        $this->addSql('CREATE INDEX IDX_225BC2BFAABE42E7 ON x_main.a_survey_completion_achievement (survey_category_id)');
        $this->addSql('CREATE INDEX IDX_225BC2BF9F2C3FAB ON x_main.a_survey_completion_achievement (zone_id)');
        $this->addSql('CREATE TABLE x_main.a_photo_upload_achievement (id INT NOT NULL, survey_id INT NOT NULL, uuid UUID NOT NULL, threshold SMALLINT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7CC24244D17F50A6 ON x_main.a_photo_upload_achievement (uuid)');
        $this->addSql('CREATE INDEX IDX_7CC24244B3FE509D ON x_main.a_photo_upload_achievement (survey_id)');
        $this->addSql('CREATE TABLE x_main.a_achievement (id INT NOT NULL, uuid UUID NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8DCC3731D17F50A6 ON x_main.a_achievement (uuid)');
        $this->addSql('CREATE TABLE x_main.a_result (user_id INT NOT NULL, achievement_id INT NOT NULL, count SMALLINT NOT NULL, is_completed BOOLEAN NOT NULL, PRIMARY KEY(user_id, achievement_id))');
        $this->addSql('CREATE INDEX IDX_50416C64A76ED395 ON x_main.a_result (user_id)');
        $this->addSql('CREATE INDEX IDX_50416C64B3EC99FE ON x_main.a_result (achievement_id)');
        $this->addSql('ALTER TABLE x_main.a_survey_completion_achievement ADD CONSTRAINT FK_225BC2BFAABE42E7 FOREIGN KEY (survey_category_id) REFERENCES x_survey.survey_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_main.a_survey_completion_achievement ADD CONSTRAINT FK_225BC2BF9F2C3FAB FOREIGN KEY (zone_id) REFERENCES x_geometry.polygon (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_main.a_photo_upload_achievement ADD CONSTRAINT FK_7CC24244B3FE509D FOREIGN KEY (survey_id) REFERENCES x_survey.survey (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_main.a_result ADD CONSTRAINT FK_50416C64A76ED395 FOREIGN KEY (user_id) REFERENCES x_main.user_base (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_main.a_result ADD CONSTRAINT FK_50416C64B3EC99FE FOREIGN KEY (achievement_id) REFERENCES x_main.a_achievement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('COMMENT ON COLUMN x_survey.response_location.coordinates IS NULL');
        $this->addSql('DROP INDEX x_geometry.point_coordinates_idx');
        $this->addSql('COMMENT ON COLUMN x_geometry.point.coordinates IS NULL');
        $this->addSql('COMMENT ON COLUMN x_geometry.point.metadata IS NULL');
        $this->addSql('DROP INDEX x_geometry.line_coordinates_idx');
        $this->addSql('COMMENT ON COLUMN x_geometry.line.coordinates IS NULL');
        $this->addSql('COMMENT ON COLUMN x_geometry.line.metadata IS NULL');
        $this->addSql('DROP INDEX x_geometry.polygon_coordinates_idx');
        $this->addSql('COMMENT ON COLUMN x_geometry.polygon.coordinates IS NULL');
        $this->addSql('COMMENT ON COLUMN x_geometry.polygon.metadata IS NULL');
        $this->addSql('DROP INDEX x_geometry.geometry_base_coordinates_idx');
        $this->addSql('COMMENT ON COLUMN x_geometry.geometry_base.coordinates IS NULL');
        $this->addSql('COMMENT ON COLUMN x_geometry.geometry_base.metadata IS NULL');
        $this->addSql('DROP INDEX x_geometry.multiline_coordinates_idx');
        $this->addSql('COMMENT ON COLUMN x_geometry.multiline.coordinates IS NULL');
        $this->addSql('COMMENT ON COLUMN x_geometry.multiline.metadata IS NULL');
        $this->addSql('ALTER TABLE x_geospatial.style_group ALTER is_for_internal_system DROP DEFAULT');
        $this->addSql('ALTER TABLE x_geospatial.style_group ALTER description DROP DEFAULT');
        $this->addSql('ALTER INDEX x_geospatial.simplify_zoom_idx RENAME TO IDX_119BC836B72B7974');
        $this->addSql('ALTER TABLE x_geospatial.style_condition ALTER is_dynamic DROP DEFAULT');
        $this->addSql('ALTER TABLE x_geospatial.style_condition ALTER description DROP DEFAULT');
        $this->addSql('ALTER INDEX x_geospatial.object_type_visibility_zoom_idx RENAME TO IDX_5D88C6C1B72B7974');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE x_main.a_result DROP CONSTRAINT FK_50416C64B3EC99FE');
        $this->addSql('DROP TABLE x_main.a_survey_completion_achievement');
        $this->addSql('DROP TABLE x_main.a_photo_upload_achievement');
        $this->addSql('DROP TABLE x_main.a_achievement');
        $this->addSql('DROP TABLE x_main.a_result');
        $this->addSql('COMMENT ON COLUMN x_survey.response_location.coordinates IS \'(DC2Type:point)\'');
        $this->addSql('COMMENT ON COLUMN x_geometry.point.coordinates IS \'(DC2Type:geography)\'');
        $this->addSql('COMMENT ON COLUMN x_geometry.point.metadata IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE INDEX point_coordinates_idx ON x_geometry.point (coordinates)');
        $this->addSql('COMMENT ON COLUMN x_geometry.line.coordinates IS \'(DC2Type:geography)\'');
        $this->addSql('COMMENT ON COLUMN x_geometry.line.metadata IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE INDEX line_coordinates_idx ON x_geometry.line (coordinates)');
        $this->addSql('COMMENT ON COLUMN x_geometry.polygon.coordinates IS \'(DC2Type:geography)\'');
        $this->addSql('COMMENT ON COLUMN x_geometry.polygon.metadata IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE INDEX polygon_coordinates_idx ON x_geometry.polygon (coordinates)');
        $this->addSql('COMMENT ON COLUMN x_geometry.geometry_base.coordinates IS \'(DC2Type:geography)\'');
        $this->addSql('COMMENT ON COLUMN x_geometry.geometry_base.metadata IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE INDEX geometry_base_coordinates_idx ON x_geometry.geometry_base (coordinates)');
        $this->addSql('COMMENT ON COLUMN x_geometry.multiline.coordinates IS \'(DC2Type:geography)\'');
        $this->addSql('COMMENT ON COLUMN x_geometry.multiline.metadata IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE INDEX multiline_coordinates_idx ON x_geometry.multiline (coordinates)');
        $this->addSql('ALTER TABLE x_geospatial.style_condition ALTER is_dynamic SET DEFAULT \'false\'');
        $this->addSql('ALTER TABLE x_geospatial.style_condition ALTER description SET DEFAULT \'\'');
        $this->addSql('ALTER TABLE x_geospatial.style_group ALTER is_for_internal_system SET DEFAULT \'false\'');
        $this->addSql('ALTER TABLE x_geospatial.style_group ALTER description SET DEFAULT \'\'');
        $this->addSql('ALTER INDEX x_geospatial.idx_119bc836b72b7974 RENAME TO simplify_zoom_idx');
        $this->addSql('ALTER INDEX x_geospatial.idx_5d88c6c1b72b7974 RENAME TO object_type_visibility_zoom_idx');
    }
}
