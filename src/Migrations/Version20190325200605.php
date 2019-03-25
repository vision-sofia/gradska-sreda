<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190325200605 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create materialized view x_survey.geo_object_question';
    }

    public function up(Schema $schema) : void
    {
        foreach (explode('---', file_get_contents(__DIR__ . '/sql/20190325200605_up.sql')) as $sql) {
            $this->addSql($sql);
        }
    }

    public function down(Schema $schema) : void
    {
        foreach (explode('---', file_get_contents(__DIR__ . '/sql/20190325200605_down.sql')) as $sql) {
            $this->addSql($sql);
        }
    }
}
