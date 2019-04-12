<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190412103435 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE x_main.a_achievement (id INT GENERATED ALWAYS AS IDENTITY, uuid UUID NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8DCC3731D17F50A6 ON x_main.a_achievement (uuid)');
        $this->addSql('CREATE TABLE x_main.a_result (user_id INT NOT NULL, achievement_id INT NOT NULL, count SMALLINT NOT NULL, is_completed BOOLEAN NOT NULL, PRIMARY KEY(user_id, achievement_id))');
        $this->addSql('CREATE INDEX IDX_50416C64A76ED395 ON x_main.a_result (user_id)');
        $this->addSql('CREATE INDEX IDX_50416C64B3EC99FE ON x_main.a_result (achievement_id)');
        $this->addSql('CREATE TABLE x_main.a_photo_upload_achievement (id INT NOT NULL, survey_id INT NOT NULL, uuid UUID NOT NULL, threshold SMALLINT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) INHERITS (x_main.a_achievement)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7CC24244D17F50A6 ON x_main.a_photo_upload_achievement (uuid)');
        $this->addSql('CREATE INDEX IDX_7CC24244B3FE509D ON x_main.a_photo_upload_achievement (survey_id)');
        $this->addSql('CREATE TABLE x_main.a_survey_completion_achievement (id INT NOT NULL, survey_category_id INT NOT NULL, zone_id INT DEFAULT NULL, uuid UUID NOT NULL, threshold SMALLINT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) INHERITS (x_main.a_achievement)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_225BC2BFD17F50A6 ON x_main.a_survey_completion_achievement (uuid)');
        $this->addSql('CREATE INDEX IDX_225BC2BFAABE42E7 ON x_main.a_survey_completion_achievement (survey_category_id)');
        $this->addSql('CREATE INDEX IDX_225BC2BF9F2C3FAB ON x_main.a_survey_completion_achievement (zone_id)');
        $this->addSql('ALTER TABLE x_main.a_result ADD CONSTRAINT FK_50416C64A76ED395 FOREIGN KEY (user_id) REFERENCES x_main.user_base (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_main.a_result ADD CONSTRAINT FK_50416C64B3EC99FE FOREIGN KEY (achievement_id) REFERENCES x_main.a_achievement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_main.a_photo_upload_achievement ADD CONSTRAINT FK_7CC24244B3FE509D FOREIGN KEY (survey_id) REFERENCES x_survey.survey (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_main.a_survey_completion_achievement ADD CONSTRAINT FK_225BC2BFAABE42E7 FOREIGN KEY (survey_category_id) REFERENCES x_survey.survey_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_main.a_survey_completion_achievement ADD CONSTRAINT FK_225BC2BF9F2C3FAB FOREIGN KEY (zone_id) REFERENCES x_geometry.polygon (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE x_main.a_result DROP CONSTRAINT FK_50416C64B3EC99FE');
        $this->addSql('DROP TABLE x_main.a_result');
        $this->addSql('DROP TABLE x_main.a_photo_upload_achievement');
        $this->addSql('DROP TABLE x_main.a_achievement');
        $this->addSql('DROP TABLE x_main.a_survey_completion_achievement');
    }
}
