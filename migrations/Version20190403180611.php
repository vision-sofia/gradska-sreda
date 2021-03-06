<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190403180611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create materialized view x_survey.ev_criterion_question and refresh trigger';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        foreach (explode('---', file_get_contents(__DIR__ . '/sql/20190403180611_up.sql')) as $sql) {
            $this->addSql($sql);
        }
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        foreach (explode('---', file_get_contents(__DIR__ . '/sql/20190403180611_down.sql')) as $sql) {
            $this->addSql($sql);
        }
    }
}
