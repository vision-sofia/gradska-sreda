<?php


namespace App\Services\Geospatial\StyleBuilder;

use App\AppMain\Entity\Geospatial\StyleCondition;
use Doctrine\ORM\EntityManagerInterface;

class StyleBuilder
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function inherit() {

    }

    public function build():void
    {
        $chunkSize = 1000;

        $stylesConditions = $this->em->getRepository(StyleCondition::class)
            ->findBy([
                'isDynamic' => false
            ], [
                'priority' => 'ASC'
            ])
        ;

        $styles = [];

        /** @var StyleCondition $geoObject */
        foreach ($stylesConditions as $styleCondition) {
            $styles[$styleCondition->getAttribute()][] = [
                'value' => $styleCondition->getValue(),
                'base_style' => $styleCondition->getBaseStyle(),
                'hover_style' => $styleCondition->getHoverStyle(),
            ];
        }

        $styleGroups = [];

        $conn = $this->em->getConnection();
/*        $conn->query('
            UPDATE 
                x_geospatial.geo_object 
            SET 
                style_base = NULL, 
                style_hover = NULL 
        ');*/

        $conn->beginTransaction();

        $conn->query('DROP TABLE IF EXISTS temp_style');
        $conn->query('CREATE TEMP TABLE temp_style (id INT, style_base VARCHAR(32), style_hover VARCHAR(32))');

        $insertStmt = $conn->prepare($this->buildInsertSQL($chunkSize));

        $batch = [];

        $i = 0;

        foreach ($this->geoObjects() as $geoObject) {
            ++$i;

            $sk = [
                's1' => [],
                's2' => [],
                'key1' => '',
                'key2' => '',
            ];

            $geometryType = json_decode($geoObject['geometry'], true)['type'];
            $attributes = json_decode($geoObject['properties'], true);

            if ('LineString' === $geometryType || 'MultiLineString' === $geometryType) {
                $sk = $this->chk($attributes, $styles, $sk, 'line');
            } elseif ('Point' === $geometryType || 'MultiPoint' === $geometryType) {
                $sk = $this->chk($attributes, $styles, $sk, 'point');
            } elseif ('Polygon' === $geometryType || 'MultiPolygon' === $geometryType) {
                $sk = $this->chk($attributes, $styles, $sk, 'polygon');
            }

            $batch[] = $geoObject['id'];
            $batch[] = $sk['key1'];
            $batch[] = $sk['key2'];

            if (0 === $i % $chunkSize) {
                $insertStmt->execute($batch);
                $batch = [];
            }

            if (!empty($sk['key1'])) {
                $styleGroups[$sk['key1']] = $sk['s1'];
            }

            if (!empty($sk['key2'])) {
                $styleGroups[$sk['key2']] = $sk['s2'];
            }
        }

        if (!empty($batch)) {
            $remain = \count($batch) / 3;

            $stmt = $conn->prepare($this->buildInsertSQL($remain));
            $stmt->execute($batch);
        }

        $conn->query('
            UPDATE 
                x_survey.spatial_geo_object g 
            SET 
                base_style = s.style_base,
                hover_style = s.style_hover
            FROM 
                temp_style s 
            WHERE 
                s.id = g.geo_object_id 
        ');

        $conn->commit();

        $stmt = $conn->prepare('
            INSERT INTO x_geospatial.style_group(
                code,
                style,
                is_for_internal_system,
                description
            ) VALUES (
                :code,
                :style,
                false,
                \'\'
            )
            ON CONFLICT (code) DO UPDATE SET
                style = excluded.style    
        ');

        foreach ($styleGroups as $code => $geoObject) {
            $stmt->bindValue('code', $code);
            $stmt->bindValue('style', json_encode($geoObject));
            $stmt->execute();
        }

        $this->em->flush();
    }
    private function buildInsertSQL(int $chunkSize): string
    {
        $sql = 'INSERT INTO temp_style (id, style_base, style_hover) 
            VALUES ' . rtrim(str_repeat('(?, ?, ?),', $chunkSize), ',');

        return $sql;
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
        if (isset($style['base_style'][$type]['content'])) {
            $sk['s1'] = array_merge($style['base_style'][$type]['content'], $sk['s1']);
            $sk['key1'] .= $style['base_style'][$type]['code'];
        }

        if (isset($style['hover_style'][$type]['content'])) {
            $sk['s2'] = array_merge($style['hover_style'][$type]['content'], $sk['s2']);
            $sk['key2'] .= $style['hover_style'][$type]['code'];
        }

        return $sk;
    }

    private function persistDynamicStyles() {
/*        $stylesConditions = $this->em->getRepository(StyleCondition::class)
            ->findBy([
                'isDynamic' => false
            ], [
                'priority' => 'ASC'
            ])
        ;

        $stmt = $conn->prepare('
            INSERT INTO x_geospatial.style_group(
                code,
                style
            ) VALUES (
                :code,
                :style
            )
            ON CONFLICT (code) DO UPDATE SET
                style = excluded.style
        ');

        foreach ($styleGroups as $code => $geoObject) {
            $stmt->bindValue('code', $code);
            $stmt->bindValue('style', json_encode($geoObject));
            $stmt->execute();
        }*/
    }

    private function geoObjects(): \Generator
    {
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            SELECT
                g.geo_object_id as id,
                g.geo_object_name as geo_name,
                g.base_style,
                g.hover_style,
                g.object_type_name as type_name,
                g.properties,
                st_asgeojson(gb.coordinates) as geometry
            FROM
                x_survey.spatial_geo_object g
                    INNER JOIN
                x_geometry.geometry_base gb ON gb.geo_object_id = g.geo_object_id
                    INNER JOIN
                x_survey.survey s ON g.survey_id = s.id AND s.is_active = TRUE
          
        ');


        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }
}
