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

        $j = $i = 0;

        foreach ($content as $item) {
            if (\is_array($item)) {
                foreach ($item as $s) {
                    if (!isset($s['geometry']['coordinates'][0], $s['geometry']['coordinates'][1])) {
                        ++$j;

                        echo sprintf("Skip: %d\n", $j);

                        continue;
                    }

                    ++$i;

                    $name = '';

                    $stmtSPO->bindValue('attr', json_encode($s['properties']));
                    $stmtSPO->bindValue('uuid', Uuid::uuid4());
                    $stmtSPO->bindValue('name', $name);
                    $stmtSPO->bindValue('object_type_id', $objectType->getId());
                    $stmtSPO->execute();

                    $stmt->bindValue('spatial_object_id', $conn->lastInsertId());
                    $stmt->bindValue('geography', sprintf('POINT(%s %s)', $s['geometry']['coordinates'][0], $s['geometry']['coordinates'][1]));
                    $stmt->bindValue('uuid', Uuid::uuid4());
                    $stmt->execute();
                }
            }
        }

        echo sprintf("Done: %d/%d\n", $j, $i);
    }
}
