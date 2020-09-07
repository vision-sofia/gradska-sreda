<?php

namespace App\Services\Geospatial\StyleBuilder;

use App\AppMain\Entity\Geospatial\StyleCondition;
use App\AppMain\Entity\Survey\Survey\Survey;
use App\AppMain\Repository\Survey\Spatial\SurveyGeoObjectRepository;
use App\Services\Constant;
use Doctrine\ORM\EntityManagerInterface;

class StyleBuilder
{
    protected EntityManagerInterface $em;
    protected SurveyGeoObjectRepository $surveyGeoObjectRepository;

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

        /** @var Survey|null $survey */
        $survey = $this->em->getRepository(Survey::class)->findOneBy([
            'isActive' => true,
        ]);

        if (!$survey) {
            return;
        }

        $batch = [];
        $styleGroups = [];

        $i = 0;

        foreach ($this->surveyGeoObjectRepository->findBySurvey($survey->getId()) as $geoObject) {
            ++$i;

            $sk = $this->buildStyles($geoObject->properties, $geoObject->geometry_type, $styles);

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

    private function buildStyles(string $properties, string $geometryType, array $styles): array
    {
        $result = [
            's1' => [],
            's2' => [],
            'key1' => '',
            'key2' => '',
        ];

        $properties = json_decode($properties, true, 3, JSON_THROW_ON_ERROR);

        if (Constant::GEOMETRY_TYPE_LINESTRING === $geometryType
            || Constant::GEOMETRY_TYPE_MULTILINESTRING === $geometryType) {
            $result = $this->chk($properties, $styles, $result, Constant::GEOMETRY_TYPE_LINESTRING);
        } elseif (
            Constant::GEOMETRY_TYPE_POINT === $geometryType
            || Constant::GEOMETRY_TYPE_MULTIPOINT === $geometryType) {
            $result = $this->chk($properties, $styles, $result, Constant::GEOMETRY_TYPE_POINT);
        } elseif (Constant::GEOMETRY_TYPE_POLYGON === $geometryType
            || Constant::GEOMETRY_TYPE_MULTIPOLYGON === $geometryType) {
            $result = $this->chk($properties, $styles, $result, Constant::GEOMETRY_TYPE_POLYGON);
        }

        return $result;
    }

    public function buildSingle(int $surveyId, int $geoObjectId): void
    {
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

        $stmt = $conn->prepare('
            SELECT
                g.geo_object_id as id,
                g.geo_object_name as geo_name,
                g.base_style,
                g.hover_style,
                g.object_type_name as type_name,
                g.properties,
                st_asgeojson(gb.coordinates) as geometry,
                geometrytype(gb.coordinates) as geometry_type
            FROM
                x_survey.spatial_geo_object g
                    INNER JOIN
                x_geometry.geometry_base gb ON gb.geo_object_id = g.geo_object_id
                    INNER JOIN
                x_survey.survey s ON g.survey_id = s.id AND s.is_active = TRUE
            WHERE
                g.geo_object_id = ?
        ');

        $stmt->execute([$geoObjectId]);

        $geoObject = $stmt->fetch(\PDO::FETCH_OBJ);

        $sk = $this->buildStyles($geoObject->properties, $geoObject->geometry_type, $styles);

        $batch[] = $geoObject->id;
        $batch[] = $sk['key1'];
        $batch[] = $sk['key2'];

        $stmt = $conn->prepare('
            UPDATE
                x_survey.spatial_geo_object
            SET
                base_style = :base_style,
                hover_style = :hover_style
            WHERE
                geo_object_id = :geo_object_id
                AND survey_id = :survey_id
        ');

        $stmt->bindValue('base_style', $sk['key1']);
        $stmt->bindValue('hover_style', $sk['key2']);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->bindValue('survey_id', $surveyId);
        $stmt->execute();
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
