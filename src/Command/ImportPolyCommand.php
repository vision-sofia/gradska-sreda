<?php

namespace App\Command;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportPolyCommand extends Command
{
    protected static $defaultName = 'app:importp';

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

    protected function configure()
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $string = file_get_contents('/var/www/GR_Units_FullExtend.json');
        $json_a = json_decode($string, true);

        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('SELECT * FROM x_geospatial.object_type');
        $stmt->execute();

        $objectTypes = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $objectTypes[$row['name']] = $row['id'];
        }

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

        foreach ($json_a as $item) {
            if (is_array($item)) {
                foreach ($item as $s) {

                    if (isset($s['geometry']['rings'][0])) {
                        $p = [];
                        foreach ($s['geometry']['rings'][0] as $points) {
                            $p[] = implode(' ', $points);
                        }

                        $im = implode(',', $p);

                        $name = '';
                        $objectTypeId = null;

                        if (isset($s['attributes']['type'], $objectTypes[$s['attributes']['type']])) {
                            $objectTypeId = $objectTypes[$s['attributes']['type']];
                        }

                        if (isset($s['attributes']['Rajon'])) {
                            $name = $s['attributes']['Rajon'];
                        }


                        $stmtSPO->bindValue('attr', json_encode($s['attributes']));
                        $stmtSPO->bindValue('uuid', Uuid::uuid4());
                        $stmtSPO->bindValue('name', $name);
                        $stmtSPO->bindValue('object_type_id', $objectTypeId);
                        $stmtSPO->execute();

                        $stmt->bindValue('spatial_object_id', $conn->lastInsertId());
                        $stmt->bindValue('geography', 'POLYGON((' . $im . '))');
                        $stmt->bindValue('uuid', Uuid::uuid4());
                        $stmt->execute();
                        $i++;
                    } else {
                        $j++;

                        echo $j . ' skip' . PHP_EOL;
                    }
                }
            }
        }

        echo "Done {$j}/{$i}\n";
    }
}
