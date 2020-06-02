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

class ImportGTPointCommand extends Command
{
    protected static $defaultName = 'app:import-gt';

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
        $string = file_get_contents($this->container->getParameter('kernel.project_dir') . \DIRECTORY_SEPARATOR . 'src/DataFixtures/Raw/gt.json');
        $content = json_decode($string, true);

        $objectType = $this->entityManager
            ->getRepository(ObjectType::class)
            ->findOneBy(['name' => 'Спирка на градски транспорт'])
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
                local_properties,
                uuid,
                object_type_id,
                name
            ) VALUES (
                :properties,        
                :local_properties,        
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

                    if (!isset($s['geometry']['x'], $s['geometry']['y'])) {
                        echo sprintf("Skip: %d\n", $sc++);

                        continue;
                    }

                    ++$j;

                    $name = '';
                    $name .= $s['attributes']['КОД_С'] . ' - ' ?? '';
                    $name .= $s['attributes']['ИМЕ_С'] . ' ' ?? '';
                    $name = trim($name);

                    $localProperties = [
                        'has_vhc_other' => 1,
                    ];

                    $stmtGeo->bindValue('properties', json_encode($s['attributes'] ?? []));
                    $stmtGeo->bindValue('local_properties', json_encode($localProperties));
                    $stmtGeo->bindValue('uuid', Uuid::uuid4());
                    $stmtGeo->bindValue('name', $name);
                    $stmtGeo->bindValue('object_type_id', $objectType->getId());
                    $stmtGeo->execute();

                    $stmtSpatial->bindValue('spatial_object_id', $conn->lastInsertId());
                    $stmtSpatial->bindValue('geography', sprintf('POINT(%s %s)', $s['geometry']['x'], $s['geometry']['y']));
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
