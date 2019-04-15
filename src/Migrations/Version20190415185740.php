<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190415185740 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE x_main.a_result (user_id INT NOT NULL, achievement_id INT NOT NULL, count SMALLINT NOT NULL, is_completed BOOLEAN NOT NULL, PRIMARY KEY(user_id, achievement_id))');
        $this->addSql('CREATE INDEX IDX_50416C64A76ED395 ON x_main.a_result (user_id)');
        $this->addSql('CREATE INDEX IDX_50416C64B3EC99FE ON x_main.a_result (achievement_id)');
        $this->addSql('ALTER TABLE x_main.a_result ADD CONSTRAINT FK_50416C64A76ED395 FOREIGN KEY (user_id) REFERENCES x_main.user_base (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE x_main.a_result ADD CONSTRAINT FK_50416C64B3EC99FE FOREIGN KEY (achievement_id) REFERENCES x_main.a_achievement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE x_main.a_result');
    }
}
