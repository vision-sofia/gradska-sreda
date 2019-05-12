<?php

namespace App\Command;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportSGRCommand extends Command
{
    protected static $defaultName = 'app:import-sgr';

    protected $entityManager;
    protected $container;

    public function __construct(
        EntityManagerInterface $entityManager,
        ContainerInterface $container
    ) {
        $this->entityManager = $entityManager;
        $this->container = $container;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $file = file_get_contents($this->container->getParameter('kernel.root_dir') . \DIRECTORY_SEPARATOR . 'DataFixtures/Raw/Stroitelna_Granitsa.json');
        $content = json_decode($file, true);
        $geometry = $content['features'][0]['geometry']['rings'][0];

        $pieces = [];

        foreach ($geometry as $points) {
            $pieces[] = implode(' ', $points);
        }

        $geometryAsText = implode(',', $pieces);

        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('SELECT id FROM x_geospatial.object_type WHERE name = ?');
        $stmt->execute(['Строителна граница']);

        $objectType = $stmt->fetchColumn();

        $conn->beginTransaction();

        $stmt = $conn->prepare('
            INSERT INTO x_geometry.geometry_base (
                geo_object_id,
                coordinates, 
                metadata, 
                uuid
            ) VALUES (
                :spatial_object_id,
                :geography, 
                \'{}\', 
                :uuid                      
            )
        ');

        $stmtSPO = $conn->prepare('
            INSERT INTO x_geospatial.geo_object (
                attributes,
                uuid,
                object_type_id,
                name
            ) VALUES (
                :attr,        
                :uuid,
                :object_type_id,
                :name                 
            )
        ');

        $stmtSPO->bindValue('attr', json_encode([]));
        $stmtSPO->bindValue('uuid', Uuid::uuid4());
        $stmtSPO->bindValue('name', 'Строителна граница');
        $stmtSPO->bindValue('object_type_id', $objectType);
        $stmtSPO->execute();

        $stmt->bindValue('spatial_object_id', $conn->lastInsertId());
        $stmt->bindValue('geography', 'MULTILINESTRING((' . $geometryAsText . '))');
        $stmt->bindValue('uuid', Uuid::uuid4());
        $stmt->execute();

        $conn->commit();

        echo  "Import complete.\n";
    }
}
