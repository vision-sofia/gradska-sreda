<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190320204813 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE x_geospatial.style_group (id INT GENERATED ALWAYS AS IDENTITY, code VARCHAR(255) NOT NULL, style JSONB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5F2D15AA77153098 ON x_geospatial.style_group (code)');
        $this->addSql('COMMENT ON COLUMN x_geospatial.style_group.style IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE TABLE x_geospatial.style_condition (id INT GENERATED ALWAYS AS IDENTITY, attribute VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, base_style JSONB DEFAULT NULL, hover_style JSONB DEFAULT NULL, priority INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN x_geospatial.style_condition.base_style IS \'(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN x_geospatial.style_condition.hover_style IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1377547EB3FE509DC5020C33 ON x_survey.survey_auxiliary_object_type (survey_id, object_type_id)');
        $this->addSql('ALTER TABLE x_geospatial.geo_object ADD style_base VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE x_geospatial.geo_object ADD style_hover VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE x_geospatial.style_group');
        $this->addSql('DROP TABLE x_geospatial.style_condition');
        $this->addSql('ALTER TABLE x_geospatial.geo_object DROP style_base');
        $this->addSql('ALTER TABLE x_geospatial.geo_object DROP style_hover');
        $this->addSql('DROP INDEX UNIQ_1377547EB3FE509DC5020C33');
    }
}
