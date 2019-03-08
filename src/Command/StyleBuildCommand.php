<?php

namespace App\Command;

use App\AppMain\Entity\Geospatial\Style;
use App\AppMain\Entity\Geospatial\StyleGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class StyleBuildCommand extends Command
{
    protected static $defaultName = 'style:build';

    protected $stopwatch;
    protected $entityManager;

    public function __construct(Stopwatch $stopwatch, EntityManagerInterface $entityManager)
    {
        $this->stopwatch = $stopwatch;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $em = $this->entityManager;
        $conn = $this->entityManager->getConnection();

        $stylesSource = $this->entityManager->getRepository(Style::class)->findBy([
            'type' => 'base_normal',
        ], [
            'priority' => 'ASC',
        ])
        ;

        $styles = [];
        /** @var Style $item */
        foreach ($stylesSource as $item) {
            $styles[$item->getAttribute()][] = [
                'value' => $item->getValue(),
                'code' => $item->getCode(),
                'styles' => $item->getStyles(),
            ];
        }

        $this->stopwatch->start('a');

        $settingsStyle = [];

        $i = 0;

        $conn->query('CREATE TEMP TABLE sc(id INT, style VARCHAR(32))');

        $conn->beginTransaction();

       # $stmtUpdate = $conn->prepare('UPDATE x_geospatial.geo_object SET style = ? WHERE id = ?');
        $stmtUpdate = $conn->prepare('INSERT INTO sc (style, id) VALUES (?, ?), (?, ?), (?, ?), (?, ?), (?, ?)');
        $data = [];
        foreach ($this->geoObjects() as $item) {
            ++$i;
            $s1 = [];

            $key = '';
            $geometry = json_decode($item['geometry'], true);

            $attributes = json_decode($item['attributes'], true);

            foreach ($attributes as $attributeKey => $attributeValue) {
                if (!isset($styles[$attributeKey])) {
                    continue;
                }

                foreach ($styles[$attributeKey] as $style) {
                    if ($attributes[$attributeKey] === $style['value']) {
                        if (isset($style['styles']['line'])
                            && ('LineString' === $geometry['type'] || 'MultiLineString' === $geometry['type'])
                        ) {
                            $s1 = $style['styles']['line']['content'] + $s1;
                            $key .= $style['styles']['line']['code'];
                        } elseif (isset($style['styles']['point'])
                                  && ('Point' === $geometry['type'] || 'MultiPoint' === $geometry['type'])
                        ) {
                            $s1 = $style['styles']['point']['content'] + $s1;
                            $key .= $style['styles']['point']['code'];
                        } elseif (isset($style['styles']['polygon'])
                                  && ('Polygon' === $geometry['type'] || 'MultiPolygon' === $geometry['type'])
                        ) {
                            $s1 = $style['styles']['polygon']['content'] + $s1;
                            $key .= $style['styles']['polygon']['code'];
                        }
                    }
                }
            }



            $data[] = $key;
            $data[] = $item['id'];

            if($i % 5 ===0) {
                $stmtUpdate->execute($data);
                $data = [];
            }

            if (!empty($key)) {
                //$os = $styles[$item['style']['_s1']];
                //dump(array_merge($os, $s1));
                // $r = $item['style']['_s1'];
                // $nn = $r . '-' . $s1['key'];
                //  dump($nn);
                //   dump(array_merge($os, $s1));
                //dump($s1['key']);

                $settingsStyle[$key] = $s1;
            }

            if (0 === $i % 1000) {
                echo $i . PHP_EOL;

            }
        }

        $conn->query('
            UPDATE 
                x_geospatial.geo_object g 
            SET 
                style = s.style 
            FROM 
                sc s 
            WHERE s.id = g.id 
        ');

        $conn->commit();

        $conn->query('DELETE FROM x_geospatial.style_group');

        foreach ($settingsStyle as $key => $item) {
            $styleGroup = new StyleGroup();
            $styleGroup->setCode($key);
            $styleGroup->setStyles($item);

            $em->persist($styleGroup);
            $em->flush();
        }

        $d = $this->stopwatch->stop('a')->getDuration();

        echo sprintf("GeoObjects: %d\nDuration: %s\n", $i, $d);
    }

    private function geoObjects(): \Generator
    {
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('
            WITH g AS (
                SELECT
                    id,
                    uuid,
                    name,
                    object_type_id,
                    geometry,
                    jsonb_strip_nulls(attributes) as attributes
                FROM
                    (
                        SELECT
                            g.id,
                            g.uuid,
                            g.name,
                            g.object_type_id,
                            st_asgeojson(ST_Simplify(m.coordinates::geometry, :simplify_tolerance, true)) AS geometry,
                            jsonb_build_object(
                                \'_sca\', c.name,
                                \'_behavior\', \'survey\'
                            ) as attributes
                        FROM
                            x_geometry.geometry_base m
                                INNER JOIN
                            x_geospatial.geo_object g ON m.geo_object_id = g.id
                                INNER JOIN
                            x_survey.survey_element e ON g.object_type_id = e.object_type_id
                                INNER JOIN
                            x_survey.survey_category c ON e.category_id = c.id
                                INNER JOIN
                            x_geospatial.object_type_visibility v ON g.object_type_id = v.object_type_id
                                INNER JOIN
                            x_survey.survey s ON c.survey_id = s.id
                                    
                        UNION ALL
            
                        SELECT
                            g.id,
                            g.uuid,
                            g.name,
                            g.object_type_id,
                            st_asgeojson(ST_Simplify(m.coordinates::geometry, :simplify_tolerance, true)) AS geometry,
                            jsonb_build_object(
                                \'_behavior\', a.behavior
                            ) as attributes
                        FROM
                            x_geometry.geometry_base m
                                INNER JOIN
                            x_geospatial.geo_object g ON m.geo_object_id = g.id
                                INNER JOIN
                            x_survey.survey_auxiliary_object_type a ON g.object_type_id = a.object_type_id
                                LEFT JOIN
                            x_survey.survey s ON a.survey_id = s.id AND s.is_active = TRUE
                                INNER JOIN
                            x_geospatial.object_type_visibility v ON g.object_type_id = v.object_type_id

                    ) as w
            )
            SELECT
                g.id,
                g.uuid,
                g.name as geo_name,
                t.name as type_name,
                g.attributes,
                g.geometry
            FROM
                g
                    INNER JOIN
                x_geospatial.object_type t ON t.id = g.object_type_id
        ');

        $stmt->bindValue('simplify_tolerance', 0.1);
        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }
}
