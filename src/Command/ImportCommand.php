<?php

namespace App\Command;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';

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
        ini_set('memory_limit', '-1');

        $dataFile = $this->container->getParameter('kernel.project_dir')
            . \DIRECTORY_SEPARATOR
            . 'src/DataFixtures/Raw/network.json'
        ;

        $string = file_get_contents($dataFile);
        $content = json_decode($string, true);

        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('SELECT * FROM x_geospatial.object_type');
        $stmt->execute();

        $objectTypes = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $objectTypes[$row['name']] = $row['id'];
        }

        $conn->beginTransaction();

        $stmt = $conn->prepare('
            INSERT INTO x_geometry.geometry_base (
                geo_object_id,
                coordinates,
                metadata,
                uuid
            ) VALUES (
                :spatial_object_id,
                ST_GeomFromText(:geography, 4326),
                \'{}\',
                :uuid
            )
        ');

        $stmtSPO = $conn->prepare('
            INSERT INTO x_geospatial.geo_object (
                properties,
                uuid,
                object_type_id,
                name
            ) VALUES (
                :properties,
                :uuid,
                :object_type_id,
                :name
            )
        ');

        $j = $i = 0;

        foreach ($content as $item) {
            if (\is_array($item)) {
                foreach ($item as $s) {
                    if (isset($s['geometry']['coordinates'])) {
                        $p = [];

                        if (isset($s['geometry']['coordinates'][0][0][0])) {
                            echo 'SKIP' . PHP_EOL;
                            continue;
                        }
                        #dump($s['geometry']['coordinates'][0][0][0]);
                        foreach ($s['geometry']['coordinates'] as $points) {
                            $p[] = implode(' ', $points);
                        }

                        $im = implode(',', $p);

                        $objectTypeId = null;

                        if (isset($s['properties']['type'], $objectTypes[$s['properties']['type']])) {
                            $objectTypeId = $objectTypes[$s['properties']['type']];
                        }

                        $name = $s['properties']['name'] ?? '';
                        $properties = json_encode($s['properties'] ?? [], JSON_THROW_ON_ERROR, 512);

                        $stmtSPO->bindValue('properties', $properties);
                        $stmtSPO->bindValue('uuid', Uuid::uuid4());
                        $stmtSPO->bindValue('name', $name);
                        $stmtSPO->bindValue('object_type_id', $objectTypeId);
                        $stmtSPO->execute();

                        $stmt->bindValue('spatial_object_id', $conn->lastInsertId());
                        $stmt->bindValue('geography', 'MULTILINESTRING((' . $im . '))');
                        $stmt->bindValue('uuid', Uuid::uuid4());
                        $stmt->execute();
                        ++$i;

                        if (0 === $i % 1000) {
                            echo $i . PHP_EOL;
                        }
                    } else {
                        ++$j;

                        echo $j . ' skip' . PHP_EOL;
                    }
                }
            }
        }

        $stmt = $conn->prepare('
            INSERT INTO x_survey.spatial_scope (
                geo_object_id,
                survey_id
            )
            SELECT
                id,
                (SELECT id FROM x_survey.survey WHERE name = :name)
            FROM
                x_geospatial.geo_object
        ');

        $stmt->bindValue('name', 'Анкета');
        $stmt->execute();

        $stmt->bindValue('name', 'Анкета-2');
        $stmt->execute();

        $conn->commit();

        echo sprintf("Complete.\nSkipped objects: %d\nImported objects: %d\n", $j, $i);

        return 0;
    }
}
