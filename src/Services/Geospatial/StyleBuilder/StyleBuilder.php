<?php

namespace App\Services\Geospatial\StyleBuilder;

use App\AppMain\Entity\Geospatial\StyleCondition;
use App\AppMain\Entity\Survey\Survey\Survey;
use App\AppMain\Repository\Survey\Spatial\SurveyGeoObjectRepository;
use App\Services\Constant;
use Doctrine\ORM\EntityManagerInterface;

class StyleBuilder
{
    protected $em;
    protected $surveyGeoObjectRepository;

    public function __construct(
        EntityManagerInterface $em,
        SurveyGeoObjectRepository $surveyGeoObjectRepository
    ) {
        $this->em = $em;
        $this->surveyGeoObjectRepository = $surveyGeoObjectRepository;
    }

    public function build(): void
    {
        $chunkSize = 1000;

        $stylesConditions = $this->em->getRepository(StyleCondition::class)
            ->findBy([
                'isDynamic' => false,
            ], [
                'priority' => 'ASC',
            ])
        ;

        $styles = [];

        foreach ($stylesConditions as $styleCondition) {
            $styles[$styleCondition->getAttribute()][] = [
                'value' => $styleCondition->getValue(),
                'base_style' => $styleCondition->getBaseStyle(),
                'hover_style' => $styleCondition->getHoverStyle(),
            ];
        }

        $conn = $this->em->getConnection();

        $conn->beginTransaction();

        $conn->query('
            UPDATE 
                x_survey.spatial_geo_object 
            SET 
                base_style = NULL, 
                hover_style = NULL 
        ');

        $conn->query('
            DROP TABLE IF EXISTS temp_style
        ');

        $conn->query('
            CREATE TEMP TABLE temp_style (
                id INT, 
                base_style VARCHAR(32), 
                hover_style VARCHAR(32)
            )
        ');

        $insertStmt = $conn->prepare($this->buildInsertSQL($chunkSize));

        $survey = $this->em->getRepository(Survey::class)->findOneBy([
            'isActive' => true,
        ]);

        $batch = [];
        $styleGroups = [];

        $i = 0;

        foreach ($this->surveyGeoObjectRepository->findBySurvey($survey->getId()) as $geoObject) {
            ++$i;

            $sk = [
                's1' => [],
                's2' => [],
                'key1' => '',
                'key2' => '',
            ];

            $properties = json_decode($geoObject->properties, true);

            if (Constant::GEOMETRY_TYPE_LINESTRING === $geoObject->geometry_type
                || Constant::GEOMETRY_TYPE_MULTILINESTRING === $geoObject->geometry_type) {
                $sk = $this->chk($properties, $styles, $sk, Constant::GEOMETRY_TYPE_LINESTRING);
            } elseif (
                Constant::GEOMETRY_TYPE_POINT === $geoObject->geometry_type
                || Constant::GEOMETRY_TYPE_MULTIPOINT === $geoObject->geometry_type) {
                $sk = $this->chk($properties, $styles, $sk, Constant::GEOMETRY_TYPE_POINT);
            } elseif (Constant::GEOMETRY_TYPE_POLYGON === $geoObject->geometry_type
                || Constant::GEOMETRY_TYPE_MULTIPOLYGON === $geoObject->geometry_type) {
                $sk = $this->chk($properties, $styles, $sk, Constant::GEOMETRY_TYPE_POLYGON);
            }

            $batch[] = $geoObject->id;
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
                base_style = s.base_style,
                hover_style = s.hover_style
            FROM 
                temp_style s 
            WHERE 
                s.id = g.geo_object_id 
        ');

        $conn->commit();

        $stmt = $conn->prepare('
            INSERT INTO x_geospatial.style_group (
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

        foreach ($styleGroups as $code => $style) {
            $stmt->bindValue('code', $code);
            $stmt->bindValue('style', json_encode($style));
            $stmt->execute();
        }
    }

    private function buildInsertSQL(int $chunkSize): string
    {
        $sql = '
            INSERT INTO temp_style (
                id, 
                base_style, 
                hover_style
            ) 
            VALUES ' . rtrim(str_repeat('(?, ?, ?),', $chunkSize), ',');

        return $sql;
    }

    private function chk(array $properties, array $styles, array $sk, string $geometryType): array
    {
        // Attribute based style
        foreach ($properties as $propertyKey => $propertyValue) {
            if (!isset($styles[$propertyKey])) {
                continue;
            }

            foreach ($styles[$propertyKey] as $style) {
                if ('*' === $style['value']
                    || (string) $properties[$propertyKey] === (string) $style['value']) {
                    $sk = $this->comp($style, $sk, $geometryType);
                }
            }
        }

        // Default style
        if (empty($sk['key1']) && empty($sk['key2']) && isset($styles['_default'])) {
            foreach ($styles['_default'] as $style) {
                $sk = $this->comp($style, $sk, $geometryType);
            }
        }

        return $sk;
    }

    private function comp(array $style, array $sk, string $geometryType): array
    {
        if (isset($style['base_style'][$geometryType]['content'])) {
            $sk['s1'] = array_merge($style['base_style'][$geometryType]['content'], $sk['s1']);
            $sk['key1'] .= $style['base_style'][$geometryType]['code'];
        }

        if (isset($style['hover_style'][$geometryType]['content'])) {
            $sk['s2'] = array_merge($style['hover_style'][$geometryType]['content'], $sk['s2']);
            $sk['key2'] .= $style['hover_style'][$geometryType]['code'];
        }

        return $sk;
    }
}
