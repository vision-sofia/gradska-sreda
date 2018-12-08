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

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';

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
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $string = file_get_contents($this->container->getParameter('kernel.root_dir') . DIRECTORY_SEPARATOR . 'DataFixtures/LULIN_TEST_WGS84.json');
        $json_a = json_decode($string, true);

        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();
        $stmt = $conn->prepare('
            INSERT INTO x_geometry.multiline (
                spatial_object_id,
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
            INSERT INTO x_geospatial.geospatial_object (
                attributes,
                uuid
            ) VALUES (
                :attr,        
                :uuid                      
            )
        ');


        foreach ($json_a as $item) {
            if (is_array($item)) {
             #   print_r($item);

                foreach ($item as $s) {
                    if (isset($s['geometry'])) {
                        $p = [];
                        foreach ($s['geometry']['coordinates'] as $points) {
                            $p[] = implode(' ', $points);
                        }

                        $im = implode(',', $p);

                        $stmtSPO->bindValue('attr', json_encode($s['properties']));
                        $stmtSPO->bindValue('uuid', Uuid::uuid4());
                        $stmtSPO->execute();

                        $stmt->bindValue('spatial_object_id', $conn->lastInsertId());
                        $stmt->bindValue('geography', 'MULTILINESTRING((' . $im . '))');
                        $stmt->bindValue('uuid', Uuid::uuid4());
                        $stmt->execute();
                    }
                }
            }
        }
    }
}
