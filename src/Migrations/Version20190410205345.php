<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190410205345 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE x_main.a_survey_completion_achievement (id INT GENERATED ALWAYS AS IDENTITY, survey_category_id INT NOT NULL, zone_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, threshold NUMERIC(2, 1) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_225BC2BFD17F50A6 ON x_main.a_survey_completion_achievement (uuid)');
        $this->addSql('CREATE INDEX IDX_225BC2BFAABE42E7 ON x_main.a_survey_completion_achievement (survey_category_id)');
        $this->addSql('CREATE INDEX IDX_225BC2BF9F2C3FAB ON x_main.a_survey_completion_achievement (zone_id)');
        $this->addSql('ALTER TABLE x_main.a_survey_completion_achievement ADD CONSTRAINT FK_225BC2BFAABE42E7 FOREIGN KEY (survey_category_id) REFERENCES x_survey.survey_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_main.a_survey_completion_achievement ADD CONSTRAINT FK_225BC2BF9F2C3FAB FOREIGN KEY (zone_id) REFERENCES x_geometry.polygon (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE x_main.a_survey_completion_achievement');
    }
}
