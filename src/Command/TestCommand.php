<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class TestCommand extends Command
{
    protected static $defaultName = 'test';

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
        $this->stopwatch->start('a');

        $styles = [
            '_sca' => [
                [
                    'value' => 'Пешеходни отсечки',
                    'code' => 'a1',
                    'style' => [
                        'color' => '#0099ff',
                        'weight' => 7,
                        'opacity' => 0.9
                    ]
                ],
                [
                    'value' => 'Алеи',
                    'code' => 'a4',
                    'style' => [
                        'color' => '#33cc33',
                        'weight' => 7,
                    ]
                ],
                [
                    'value' => 'Пресичания',
                    'code' => 'a7',
                    'style' => [
                        'color' => '#ff3300',
                        'weight' => 7,
                    ]
                ],
            ],
            '_behavior' => [
                [
                    'value' => 'survey',
                    'code' => 'op',
                    'style' => [
                        'weight' => 8,
                        'opacity' => 0.7,
                    ]
                ],
            ],
        ];

        $settingsStyle = [];

        $i = 0;

        foreach ($this->geoObjects() as $item) {
            $i++;
            $s1 = [
            ];

            $key = '';

            $attributes = json_decode($item['attributes'], true);
            // unset($a['_s1'], $a['_s2'], $a['id'], $a['name'], $a['type']);

            foreach ($attributes as $attributeKey => $attributeValue) {
                if (!isset($styles[$attributeKey])) {
                    continue;
                }

                foreach ($styles[$attributeKey] as $style) {
                    if ($attributes[$attributeKey] === $style['value']) {
                        $s1 = $style['style'] + $s1;
                        $key .= $style['code'];
                    }
                }
            }



            //dump($s1);

            //  dump($s1);

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

            //  print_r($s1);
        }

        $d = $this->stopwatch->stop('a')->getDuration();

        print_r($settingsStyle);

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
