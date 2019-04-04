<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190404131948 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE x_survey.response_answer DROP CONSTRAINT FK_6440E5CE1E27F6BF');
        $this->addSql('ALTER TABLE x_survey.response_answer ADD CONSTRAINT FK_6440E5CE1E27F6BF FOREIGN KEY (question_id) REFERENCES x_survey.response_question (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE x_survey.response_answer DROP CONSTRAINT fk_6440e5ce1e27f6bf');
        $this->addSql('ALTER TABLE x_survey.response_answer ADD CONSTRAINT fk_6440e5ce1e27f6bf FOREIGN KEY (question_id) REFERENCES x_survey.response_question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
