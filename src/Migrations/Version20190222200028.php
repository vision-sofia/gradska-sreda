<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190222200028 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE x_survey.q_answer ADD is_photo_enabled BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE x_survey.response_answer ADD explanation TEXT NOT NULL');
        $this->addSql('ALTER TABLE x_survey.response_answer ADD photo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE x_survey.response_answer DROP explanation');
        $this->addSql('ALTER TABLE x_survey.response_answer DROP photo');
        $this->addSql('ALTER TABLE x_survey.q_answer DROP is_photo_enabled');
    }
}
