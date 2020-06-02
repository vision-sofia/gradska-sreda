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

    protected EntityManagerInterface $entityManager;
    protected ContainerInterface $container;

    public function __construct(
        EntityManagerInterface $entityManager,
        ContainerInterface $container
    ) {
        $this->entityManager = $entityManager;
        $this->container = $container;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = file_get_contents($this->container->getParameter('kernel.project_dir') . \DIRECTORY_SEPARATOR . 'src/DataFixtures/Raw/Stroitelna_Granitsa.json');
        $content = json_decode($file, true);
        $geometry = $content['features'][0]['geometry']['rings'][0];

        $pieces = [];

        foreach ($geometry as $points) {
            $pieces[] = implode(' ', $points);
        }

        $geometryAsText = implode(',', $pieces);

        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();

        $stmtSpatial = $conn->prepare('SELECT id FROM x_geospatial.object_type WHERE name = ?');
        $stmtSpatial->execute(['Строителна граница']);

        $objectType = $stmtSpatial->fetchColumn();

        $conn->beginTransaction();

        $stmtGeo = $conn->prepare('
            INSERT INTO x_geospatial.geo_object (
                local_properties,
                uuid,
                object_type_id,
                name
            ) VALUES (     
                :local_properties,
                :uuid,
                :object_type_id,
                :name                 
            )
        ');

        $stmtSpatial = $conn->prepare('
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

        $localProperties = [
            '_tc' => 'sgr',
        ];

        $stmtGeo->bindValue('local_properties', json_encode($localProperties));
        $stmtGeo->bindValue('uuid', Uuid::uuid4());
        $stmtGeo->bindValue('name', 'Строителна граница');
        $stmtGeo->bindValue('object_type_id', $objectType);
        $stmtGeo->execute();

        $stmtSpatial->bindValue('spatial_object_id', $conn->lastInsertId());
        $stmtSpatial->bindValue('geography', 'MULTILINESTRING((' . $geometryAsText . '))');
        $stmtSpatial->bindValue('uuid', Uuid::uuid4());
        $stmtSpatial->execute();

        $conn->commit();

        echo  "Import complete.\n";

        return 0;
    }
}
