<?php

namespace App\Command;

use App\AppMain\Entity\Geospatial\StyleCondition;
use App\AppMain\Entity\Geospatial\StyleGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class StyleBuildV2Command extends Command
{
    protected static $defaultName = 'style:build2';

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
        // TODO: refactor to services
        $em = $this->entityManager;

        $stylesSource = $em->getRepository(StyleCondition::class)->findBy([], [
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



        $settingsStyle = [];

        $i = 0;

        $batch = 1;

        $conn = $this->entityManager->getConnection();

        $conn->query('
            UPDATE 
                x_geospatial.geo_object 
            SET 
                style_base = NULL, 
                style_hover = NULL 
        ');

        $this->stopwatch->start('style_build');

        $conn->beginTransaction();

        $conn->query('CREATE TEMP TABLE sc(id INT, style_base VARCHAR(32), style_hover VARCHAR(32))');

        $sql = 'INSERT INTO sc (id, style_base, style_hover) 
        VALUES ' . rtrim(str_repeat('(?, ?, ?),', $batch), ',');

        $stmtInsert = $conn->prepare($sql);

        $data = [];

        foreach ($this->geoObjects() as $item) {
            ++$i;

            $sk = [
                's1' => [],
                's2' => [],
                'key1' => '',
                'key2' => '',
            ];

            $geometryType = json_decode($item['geometry'], true)['type'];
            $attributes = json_decode($item['attributes'], true);

            if ('LineString' === $geometryType || 'MultiLineString' === $geometryType) {
                $sk = $this->chk($attributes, $styles, $sk, 'line');
            } elseif ('Point' === $geometryType || 'MultiPoint' === $geometryType) {
                $sk = $this->chk($attributes, $styles, $sk, 'point');
            } elseif ('Polygon' === $geometryType || 'MultiPolygon' === $geometryType) {
                $sk = $this->chk($attributes, $styles, $sk, 'polygon');
            }

            $data[] = $item['id'];
            $data[] = $sk['key1'];
            $data[] = $sk['key2'];

            if (0 === $i % $batch) {
                $stmtInsert->execute($data);
                $data = [];
            }

            if (!empty($sk['key1'])) {
                $settingsStyle[$sk['key1']] = $sk['s1'];
            }

            if (!empty($sk['key2'])) {
                $settingsStyle[$sk['key2']] = $sk['s2'];
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

        $duration = $this->stopwatch->stop('style_build')->getDuration();

        foreach ($settingsStyle as $key => $item) {
            $styleGroup = new StyleGroup();
            $styleGroup->setCode($key);
            $styleGroup->setStyles($item);

            $em->persist($styleGroup);
        }

        $em->flush();

        echo sprintf("GeoObjects: %d\nDuration: %s\n", $i, $duration);
    }

    private function chk($attributes, $styles, $sk, $type)
    {
        // Attribute based style
        foreach ($attributes as $attributeKey => $attributeValue) {
            if (!isset($styles[$attributeKey])) {
                continue;
            }

            foreach ($styles[$attributeKey] as $style) {
                if ('*' === $style['value']
                    || (string) $attributes[$attributeKey] === (string) $style['value']) {
                    $sk = $this->comp($style, $sk, $type);
                }
            }
        }

        // Default style
        if (empty($sk['key1']) && empty($sk['key2']) && isset($styles['_default'])) {
            foreach ($styles['_default'] as $style) {
                $sk = $this->comp($style, $sk, $type);
            }
        }

        return $sk;
    }

    private function comp($style, array $sk, $type): array
    {
        if ('base' === $style['style_type']) {
            $sk['s1'] = $style['styles'][$type]['content'] + $sk['s1'];
            $sk['key1'] .= $style['styles'][$type]['code'];
        } elseif ('hover' === $style['style_type']) {
            $sk['s2'] = $style['styles'][$type]['content'] + $sk['s2'];
            $sk['key2'] .= $style['styles'][$type]['code'];
        }

        return $sk;
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
                        WHERE
                            s.is_active = TRUE    
                                    
                        UNION ALL
            
                        SELECT
                            g.id,
                            g.uuid,
                            g.name,
                            g.object_type_id,
                            st_asgeojson(ST_Simplify(m.coordinates::geometry, :simplify_tolerance, true)) AS geometry,
                            jsonb_build_object(
                                \'_behavior\', a.behavior,
                                \'has_vhc_other\', g.attributes->\'has_vhc_other\',
                                \'has_vhc_metro\', g.attributes->\'has_vhc_metro\'
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
                        WHERE
                            s.is_active = TRUE                             

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
