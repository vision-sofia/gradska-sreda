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

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $string = file_get_contents($this->container->getParameter('kernel.root_dir') . \DIRECTORY_SEPARATOR . 'DataFixtures/Raw/gt.json');
        $content = json_decode($string, true);

        $objectType = $this->entityManager
            ->getRepository(ObjectType::class)
            ->findOneBy(['name' => 'Спирка на градски транспорт'])
        ;

        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();

        $stmtInsGeometry = $conn->prepare('
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

        $stmtInsertGeoObject = $conn->prepare('
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

        $sc = $j = $i = 0;

        foreach ($content as $item) {
            if (\is_array($item)) {
                foreach ($item as $s) {
                    ++$i;

                    if (!isset($s['geometry']['x'], $s['geometry']['y'])) {
                        dump($s);
                        echo sprintf("Skip: %d\n", $sc++);

                        continue;
                    }

                    ++$j;

                    $name = isset($s['properties']['ИМЕ__1']) ? $s['properties']['ИМЕ__1'] : '';

                    $s['properties']['has_vhc_other'] = 1;

                    $stmtInsertGeoObject->bindValue('attr', json_encode($s['properties']));
                    $stmtInsertGeoObject->bindValue('uuid', Uuid::uuid4());
                    $stmtInsertGeoObject->bindValue('name', $name);
                    $stmtInsertGeoObject->bindValue('object_type_id', $objectType->getId());
                    $stmtInsertGeoObject->execute();

                    $stmtInsGeometry->bindValue('spatial_object_id', $conn->lastInsertId());
                    $stmtInsGeometry->bindValue('geography', sprintf('POINT(%s %s)', $s['geometry']['x'], $s['geometry']['y']));
                    $stmtInsGeometry->bindValue('uuid', Uuid::uuid4());
                    $stmtInsGeometry->execute();
                }
            }
        }

        echo sprintf("Done: %d/%d\n", $j, $i);
    }
}
