<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExportCommand extends Command
{
    protected static $defaultName = 'export';

    protected $em;
    protected $container;

    public function __construct(
        EntityManagerInterface $em,
        ContainerInterface $container
    ) {
        $this->em = $em;
        $this->container = $container;
        parent::__construct();
    }

    // TODO: Refactor in to service and reduce memory usage
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            SELECT 
                g.uuid,
                g.properties,
                st_asgeojson(gb.coordinates) as geometry
            FROM 
                x_geospatial.geo_object g
                    INNER JOIN
                x_geometry.geometry_base gb ON g.id = gb.geo_object_id
        ');

        $stmt->execute();

        $result = [];

        while ($geoObject = $stmt->fetch(\PDO::FETCH_OBJ)) {
            $properties = json_decode($geoObject->properties, false);
            $properties->uuid = $geoObject->uuid;

            unset($geoObject->uuid);

            $geoObject->type = 'Feature';
            $geoObject->properties = $properties;
            $geoObject->geometry = json_decode($geoObject->geometry, false);

            $result[] = $geoObject;
        }

        $path =
            $this->container->getParameter('kernel.root_dir') .
            \DIRECTORY_SEPARATOR .
            'DataFixtures/Raw/_export.json';

        file_put_contents($path, json_encode($result, JSON_UNESCAPED_UNICODE));

        unset($result);

        $io->success('Done');
    }
}
