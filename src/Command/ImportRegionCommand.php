<?php

namespace App\Command;

use App\AppMain\Entity\Geospatial\ObjectType;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportRegionCommand extends Command
{
    protected static $defaultName = 'app:import-r';

    protected EntityManagerInterface $entityManager;
    protected string $projectDir;

    public function __construct(EntityManagerInterface $entityManager, string $projectDir) {
        $this->entityManager = $entityManager;
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $string = file_get_contents($this->projectDir . \DIRECTORY_SEPARATOR . 'src/DataFixtures/Raw/regions.json');
        $content = json_decode($string, true);

        $objectType = $this->entityManager
            ->getRepository(ObjectType::class)
            ->findOneBy(['name' => 'Административен райони'])
        ;

        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();

        $conn->beginTransaction();

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

        $stmtGeo = $conn->prepare('
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

        $sc = $j = $i = 0;

        foreach ($content as $item) {
            if (\is_array($item)) {
                foreach ($item as $s) {
                    ++$i;

                    if (!isset($s['geometry']['rings'])) {
                        echo sprintf("Skip: %d\n", $sc++);

                        continue;
                    }

                    ++$j;

                    $p = [];
                    foreach ($s['geometry']['rings'] as $points) {
                        foreach ($points as $point) {
                            if (empty($point[0]) || empty($point[1])) {
                                continue;
                            }

                            $p[] = implode(' ', $point);
                        }
                    }

                    $im = implode(',', $p);

                    $name = isset($s['attributes']['OBNS_LAT']) ? $s['attributes']['OBNS_LAT'] : '';

                    $stmtGeo->bindValue('properties', json_encode($s['attributes'] ?? []));
                    $stmtGeo->bindValue('uuid', Uuid::uuid4());
                    $stmtGeo->bindValue('name', $name);
                    $stmtGeo->bindValue('object_type_id', $objectType->getId());
                    $stmtGeo->execute();

                    $stmtSpatial->bindValue('spatial_object_id', $conn->lastInsertId());
                    $stmtSpatial->bindValue('geography', 'POLYGON((' . $im . '))');
                    $stmtSpatial->bindValue('uuid', Uuid::uuid4());
                    $stmtSpatial->execute();
                }
            }
        }

        $conn->commit();

        echo sprintf("Done: %d/%d\n", $j, $i);

        return 0;
    }
}
