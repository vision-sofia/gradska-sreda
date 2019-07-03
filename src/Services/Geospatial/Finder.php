<?php

namespace App\Services\Geospatial;

use App\AppMain\DTO\SurveyGeoObjectDTO;
use App\Services\Geometry\Utils;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;

class Finder
{
    protected $em;
    protected $utils;

    public function __construct(EntityManagerInterface $entityManager, Utils $utils)
    {
        $this->em = $entityManager;
        $this->utils = $utils;
    }

    public function find(int $zoom, float $simplifyTolerance, string $in, int $surveyId): \Generator
    {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        $sql = /** @lang PostgreSQL */
            '
            SELECT
                g.uuid,
                g.geo_object_name as geo_name,
                g.base_style,
                g.hover_style,
                g.object_type_name as type_name,
                g.properties,
                m.geometry
            FROM
                x_geometry.simplified_geo m
                    INNER JOIN
                x_survey.spatial_geo_object g ON m.geo_object_id = g.geo_object_id
                    INNER JOIN
                x_geospatial.object_type_visibility v ON g.object_type_id = v.object_type_id
            WHERE
                g.survey_id = :survey_id
                AND m.coordinates && ST_MakeEnvelope(:x_min, :y_min, :x_max, :y_max)
                AND v.zoom @> :zoom::int
                AND m.simplify_tolerance = :simplify_tolerance

        ';

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('x_min', $this->utils->bbox($in, 0));
        $stmt->bindValue('y_min', $this->utils->bbox($in, 1));
        $stmt->bindValue('x_max', $this->utils->bbox($in, 2));
        $stmt->bindValue('y_max', $this->utils->bbox($in, 3));
        $stmt->bindValue('zoom', $zoom, \PDO::PARAM_INT);
        $stmt->bindValue('simplify_tolerance', $simplifyTolerance);
        $stmt->bindValue('survey_id', $surveyId);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, SurveyGeoObjectDTO::class);

        while ($row = $stmt->fetch()) {
            yield $row;
        }
    }

    public function userSubmitted(int $userId, float $simplifyTolerance): \Generator
    {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            SELECT
                g.geo_object_id,
                g.uuid,
                g.base_style,
                g.hover_style,
                g.geo_object_name as geo_name,
                t.name as type_name,
                ST_AsGeoJSON(ST_Simplify(gb.coordinates::geometry, :simplify_tolerance, true)) AS geometry,
                jsonb_build_object(
                    \'_geo_comp\', uc.is_completed::int
                ) as properties
            FROM
                x_survey.result_user_completion uc
                    INNER JOIN
                x_survey.spatial_geo_object g ON uc.geo_object_id = g.geo_object_id
                    INNER JOIN
                x_geometry.geometry_base gb ON g.geo_object_id = gb.geo_object_id
                    INNER JOIN
                x_geospatial.object_type t ON g.object_type_id = t.id
            WHERE
                user_id = :user_id
        ');

        $stmt->bindValue('simplify_tolerance', $simplifyTolerance);
        $stmt->bindValue('user_id', $userId);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, SurveyGeoObjectDTO::class);

        while ($row = $stmt->fetch()) {
            yield $row;
        }
    }

    public function userGeoCollection(int $userId, float $simplifyTolerance): \Generator
    {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            SELECT
                g.geo_object_id,
                g.uuid,
                g.base_style,
                g.hover_style,
                g.geo_object_name as geo_name,
                t.name as type_name,
                c.uuid as geo_collection_uuid,     
                ST_AsGeoJSON(ST_Simplify(gb.coordinates::geometry, :simplify_tolerance, true)) AS geometry,
                jsonb_build_object(
                    \'_gc\', 1,
                    \'_gc_id\', c.uuid,
                    \'_behavior\', \'survey\'
                ) as properties
            FROM
                x_survey.gc_collection c
                    INNER JOIN
                x_survey.gc_collection_content cc ON c.id = cc.geo_collection_id
                    INNER JOIN
                x_survey.spatial_geo_object g ON cc.geo_object_id = g.geo_object_id
                    INNER JOIN
                x_geometry.geometry_base gb ON g.geo_object_id = gb.geo_object_id
                    INNER JOIN
                x_geospatial.object_type t ON g.object_type_id = t.id
            WHERE
                c.user_id = :user_id
        ');

        $stmt->bindValue('simplify_tolerance', $simplifyTolerance);
        $stmt->bindValue('user_id', $userId);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, SurveyGeoObjectDTO::class);

        while ($row = $stmt->fetch()) {
            yield $row;
        }
    }

    public function findSelected(string $geoObjectUuid): ?SurveyGeoObjectDTO
    {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            SELECT
                g.geo_object_id,
                g.uuid,
                -- g.base_style,
                -- g.hover_style,
                \'on_dialog_line\' as base_style,
                \'on_dialog_line\' as hover_style,
                g.geo_object_name as geo_name,
                g.object_type_name as type_name,
                ST_AsGeoJSON(gb.coordinates::geometry) AS geometry,
                jsonb_build_object(
                    \'_behavior\', \'survey\'
                ) as properties
            FROM
                x_survey.spatial_geo_object g
                    INNER JOIN
                x_geometry.geometry_base gb ON g.geo_object_id = gb.geo_object_id
            WHERE
                g.uuid = ?
        ');

        $stmt->execute([$geoObjectUuid]);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, SurveyGeoObjectDTO::class);

        return $stmt->fetch();
    }
}
