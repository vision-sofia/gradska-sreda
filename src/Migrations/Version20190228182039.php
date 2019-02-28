<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190228182039 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE x_survey.gc_collection (id INT GENERATED ALWAYS AS IDENTITY, user_id INT NOT NULL, survey_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CD4BF60AD17F50A6 ON x_survey.gc_collection (uuid)');
        $this->addSql('CREATE INDEX IDX_CD4BF60AA76ED395 ON x_survey.gc_collection (user_id)');
        $this->addSql('CREATE INDEX IDX_CD4BF60AB3FE509D ON x_survey.gc_collection (survey_id)');
        $this->addSql('CREATE TABLE x_survey.gc_collection_content (id INT GENERATED ALWAYS AS IDENTITY, geo_collection_id INT NOT NULL, geo_object_id INT NOT NULL, added_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_68F9143AD17F50A6 ON x_survey.gc_collection_content (uuid)');
        $this->addSql('CREATE INDEX IDX_68F9143AE0946303 ON x_survey.gc_collection_content (geo_collection_id)');
        $this->addSql('CREATE INDEX IDX_68F9143A82127C22 ON x_survey.gc_collection_content (geo_object_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_68F9143AE094630382127C22 ON x_survey.gc_collection_content (geo_collection_id, geo_object_id)');
        $this->addSql('ALTER TABLE x_survey.gc_collection ADD CONSTRAINT FK_CD4BF60AA76ED395 FOREIGN KEY (user_id) REFERENCES x_main.user_base (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.gc_collection ADD CONSTRAINT FK_CD4BF60AB3FE509D FOREIGN KEY (survey_id) REFERENCES x_survey.survey (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.gc_collection_content ADD CONSTRAINT FK_68F9143AE0946303 FOREIGN KEY (geo_collection_id) REFERENCES x_survey.gc_collection (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_survey.gc_collection_content ADD CONSTRAINT FK_68F9143A82127C22 FOREIGN KEY (geo_object_id) REFERENCES x_geospatial.geo_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE x_survey.gc_collection_content DROP CONSTRAINT FK_68F9143AE0946303');
        $this->addSql('DROP TABLE x_survey.gc_collection');
        $this->addSql('DROP TABLE x_survey.gc_collection_content');
    }
}
