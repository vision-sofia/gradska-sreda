<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190130164808 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        foreach (explode('---', file_get_contents(__DIR__ . '/sql/20190130164808_up.sql')) as $sql) {
            $this->addSql($sql);
        }
    }

    public function down(Schema $schema) : void
    {
        foreach (explode('---', file_get_contents(__DIR__ . '/sql/20190130164808_down.sql')) as $sql) {
            $this->addSql($sql);
        }
    }
}
