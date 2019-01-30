<?php


namespace App\Doctrine;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;

class MigrationEventSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            'postGenerateSchema',
        ];
    }

    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $schema = $args->getSchema();

        foreach ($schema->getTables() as $table) {
            foreach ($table->getColumns() as $column) {
                $column->setAutoincrement(false);
            }
        }

        if (!$schema->hasNamespace('public')) {
            $schema->createNamespace('public');
        }
    }
}