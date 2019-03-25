<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190325202621 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE x_geospatial.style_condition (id INT GENERATED ALWAYS AS IDENTITY, attribute VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, base_style JSONB DEFAULT NULL, hover_style JSONB DEFAULT NULL, priority INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN x_geospatial.style_condition.base_style IS \'(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN x_geospatial.style_condition.hover_style IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE TABLE x_geospatial.style_group (id INT GENERATED ALWAYS AS IDENTITY, code VARCHAR(255) NOT NULL, style JSONB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5F2D15AA77153098 ON x_geospatial.style_group (code)');
        $this->addSql('COMMENT ON COLUMN x_geospatial.style_group.style IS \'(DC2Type:json_array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE x_geospatial.style_condition');
        $this->addSql('DROP TABLE x_geospatial.style_group');
    }
}
