<?php

namespace App\Command;

use App\AppMain\Entity\Geospatial\StyleCondition;
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

        $stylesSource = $this->entityManager->getRepository(StyleCondition::class)->findBy([

        ], [
            'priority' => 'ASC',
        ])
        ;

        $styles = [];
        /** @var StyleCondition $item */
        foreach ($stylesSource as $item) {
            $styles[$item->getAttribute()][] = [
                'value' => $item->getValue(),
                'style_type' => $item->getType(),
                'styles' => $item->getStyles(),
            ];
        }



        $this->stopwatch->start('a');

        $settingsStyle = [];

        $i = 0;

        $batch = 500;

        $conn->query('
            UPDATE 
                x_geospatial.geo_object 
            SET 
                style_base = NULL, 
                style_hover = NULL 
        ');

        $conn->beginTransaction();

        $conn->query('CREATE TEMP TABLE sc(id INT, style_base VARCHAR(32), style_hover VARCHAR(32))');

        $sql = 'INSERT INTO sc (id, style_base, style_hover) 
        VALUES ' . rtrim(str_repeat('(?, ?, ?),', $batch), ',');

        $stmtInsert = $conn->prepare($sql);

        $data = [];

        foreach ($this->geoObjects() as $item) {
            ++$i;

            $s1 = [];
            $s2 = [];

            $key1 = '';
            $key2 = '';

            $geometry = json_decode($item['geometry'], true);
            $attributes = json_decode($item['attributes'], true);

            foreach ($attributes as $attributeKey => $attributeValue) {
                if (!isset($styles[$attributeKey])) {
                    continue;
                }

                foreach ($styles[$attributeKey] as $style) {
                    if ('*' === $style['value'] || $attributes[$attributeKey] === $style['value']) {
                        if (isset($style['styles']['line'])
                            && ('LineString' === $geometry['type'] || 'MultiLineString' === $geometry['type'])
                        ) {
                            if ('base' === $style['style_type']) {
                                $s1 = $style['styles']['line']['content'] + $s1;
                                $key1 .= $style['styles']['line']['code'];
                            } elseif ('hover' === $style['style_type']) {
                                $s2 = $style['styles']['line']['content'] + $s2;
                                $key2 .= $style['styles']['line']['code'];
                            }
                        } elseif (isset($style['styles']['point'])
                                   && ('Point' === $geometry['type'] || 'MultiPoint' === $geometry['type'])
                        ) {
                            if ('base' === $style['style_type']) {
                                $s1 = $style['styles']['point']['content'] + $s1;
                                $key1 .= $style['styles']['point']['code'];
                            } elseif ('hover' === $style['style_type']) {
                                $s2 = $style['styles']['point']['content'] + $s2;
                                $key2 .= $style['styles']['point']['code'];
                            }
                        } elseif (isset($style['styles']['polygon'])
                                   && ('Polygon' === $geometry['type'] || 'MultiPolygon' === $geometry['type'])
                        ) {
                            if ('base' === $style['style_type']) {
                                $s1 = $style['styles']['polygon']['content'] + $s1;
                                $key1 .= $style['styles']['polygon']['code'];
                            } elseif ('hover' === $style['style_type']) {
                                $s2 = $style['styles']['polygon']['content'] + $s2;
                                $key2 .= $style['styles']['polygon']['code'];
                            }
                        }
                    }
                }
            }

            $data[] = $item['id'];
            $data[] = $key1;
            $data[] = $key2;

            if (0 === $i % $batch) {
                $stmtInsert->execute($data);
                $data = [];
            }

            if (!empty($key1)) {
                //$os = $styles[$item['style']['_s1']];
                //dump(array_merge($os, $s1));
                // $r = $item['style']['_s1'];
                // $nn = $r . '-' . $s1['key'];
                //  dump($nn);
                //   dump(array_merge($os, $s1));
                //dump($s1['key']);

                $settingsStyle[$key1] = $s1;
            }

            if (!empty($key2)) {
                //$os = $styles[$item['style']['_s1']];
                //dump(array_merge($os, $s1));
                // $r = $item['style']['_s1'];
                // $nn = $r . '-' . $s1['key'];
                //  dump($nn);
                //   dump(array_merge($os, $s1));
                //dump($s1['key']);

                $settingsStyle[$key2] = $s2;
            }

            if (0 === $i % 1000) {
                echo $i . PHP_EOL;
            }
        }

        $conn->query('
            UPDATE 
                x_geospatial.geo_object g 
            SET 
                style_base = s.style_base,
                style_hover = s.style_hover
            FROM 
                sc s 
            WHERE 
                s.id = g.id 
        ');

        $conn->commit();

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
