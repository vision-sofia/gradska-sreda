<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190201171305 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE x_geospatial.object_type_visibility (id INT GENERATED ALWAYS AS IDENTITY, object_type_id INT NOT NULL, zoom_threshold SMALLINT NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5D88C6C1D17F50A6 ON x_geospatial.object_type_visibility (uuid)');
        $this->addSql('CREATE INDEX IDX_5D88C6C1C5020C33 ON x_geospatial.object_type_visibility (object_type_id)');
        $this->addSql('ALTER TABLE x_geospatial.object_type_visibility ADD CONSTRAINT FK_5D88C6C1C5020C33 FOREIGN KEY (object_type_id) REFERENCES x_geospatial.object_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE x_geospatial.object_type_visibility');
    }
}
