<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190505115247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE x_survey.response_question ADD is_completed BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE x_survey.response_answer ADD is_completed BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE x_survey.q_answer ADD is_child_answer_required BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE x_survey.q_answer ADD is_explanation_required BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE x_survey.q_answer ADD is_photo_required BOOLEAN DEFAULT \'false\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE x_survey.response_question DROP is_completed');
        $this->addSql('ALTER TABLE x_survey.response_answer DROP is_completed');
        $this->addSql('ALTER TABLE x_survey.q_answer ADD is_child_answer_required BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE x_survey.q_answer ADD is_explanation_required BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE x_survey.q_answer ADD is_photo_required BOOLEAN DEFAULT \'false\' NOT NULL');
    }
}
