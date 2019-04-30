<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190429192417 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE x_geospatial.simplify ADD zoom int4range NOT NULL');
        $this->addSql('ALTER TABLE x_geospatial.simplify DROP min_zoom');
        $this->addSql('ALTER TABLE x_geospatial.simplify DROP max_zoom');
        $this->addSql('CREATE INDEX ON x_geospatial.simplify USING GIST(zoom);');
        $this->addSql('COMMENT ON COLUMN x_geospatial.simplify.zoom IS \'(DC2Type:int4range)\'');
        $this->addSql('ALTER TABLE x_geospatial.object_type_visibility ADD zoom int4range NOT NULL');
        $this->addSql('ALTER TABLE x_geospatial.object_type_visibility DROP min_zoom');
        $this->addSql('ALTER TABLE x_geospatial.object_type_visibility DROP max_zoom');
        $this->addSql('CREATE INDEX ON x_geospatial.object_type_visibility USING GIST(zoom);');
        $this->addSql('COMMENT ON COLUMN x_geospatial.object_type_visibility.zoom IS \'(DC2Type:int4range)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE x_main.a_user_completion (id INT NOT NULL, user_id INT NOT NULL, achivment_id INT NOT NULL, uuid UUID NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_7e4db877b0dcac54 ON x_main.a_user_completion (achivment_id)');
        $this->addSql('CREATE INDEX idx_7e4db877a76ed395 ON x_main.a_user_completion (user_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_7e4db877d17f50a6 ON x_main.a_user_completion (uuid)');
        $this->addSql('ALTER TABLE x_main.a_user_completion ADD CONSTRAINT fk_7e4db877a76ed395 FOREIGN KEY (user_id) REFERENCES x_main.user_base (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_main.a_user_completion ADD CONSTRAINT fk_7e4db877b0dcac54 FOREIGN KEY (achivment_id) REFERENCES x_main.a_achievement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_geospatial.simplify ADD min_zoom NUMERIC(3, 1) NOT NULL');
        $this->addSql('ALTER TABLE x_geospatial.simplify ADD max_zoom NUMERIC(3, 1) NOT NULL');
        $this->addSql('ALTER TABLE x_geospatial.simplify DROP zoom');
        $this->addSql('ALTER TABLE x_geospatial.object_type_visibility ADD min_zoom NUMERIC(4, 2) NOT NULL');
        $this->addSql('ALTER TABLE x_geospatial.object_type_visibility ADD max_zoom NUMERIC(4, 2) NOT NULL');
        $this->addSql('ALTER TABLE x_geospatial.object_type_visibility DROP zoom');
    }
}
