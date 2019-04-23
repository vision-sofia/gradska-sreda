<?php

namespace App\Services\Geospatial;

use App\AppMain\Entity\User\UserInterface;
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

    public function find(float $zoom, float $simplifyTolerance, string $in, UserInterface $user = null, string $collectionId = null): \Generator
    {
        $conn = $this->em->getConnection();

        $queryParts[] = '
            WITH g AS (
                SELECT
                    id,
                    uuid,
                    name,
                    style_base,
                    style_hover,
                    object_type_id,
                    geometry,
                    jsonb_strip_nulls(attributes) as attributes
                FROM
                    (
                        SELECT
                            g.id,
                            g.uuid,
                            g.name,
                            g.style_base,
                            g.style_hover,
                            g.object_type_id,
                            ST_AsGeoJSON(ST_Simplify(m.coordinates::geometry, :simplify_tolerance, true)) AS geometry,
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
                            AND m.coordinates && ST_MakeEnvelope(:x_min, :y_min, :x_max, :y_max)
                            AND :zoom <= min_zoom AND :zoom > max_zoom

                        UNION ALL

                        SELECT
                            g.id,
                            g.uuid,
                            g.name,
                            g.style_base,
                            g.style_hover,                            
                            g.object_type_id,
                            ST_AsGeoJSON(ST_Simplify(m.coordinates::geometry, :simplify_tolerance, true)) AS geometry,
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
                            m.coordinates && ST_MakeEnvelope(:x_min, :y_min, :x_max, :y_max)
                            AND :zoom <= min_zoom AND :zoom > max_zoom
                    ) as w
            )        
        ';

        $qb = $conn->createQueryBuilder();

        $qb->select([
            'g.id',
            'g.uuid',
            'g.style_base',
            'g.style_hover',
            'g.name as geo_name',
            't.name as type_name',

            'g.attributes',
            'g.geometry',

        ]);
        $qb->from('g');
        $qb->innerJoin('g', 'x_geospatial.object_type', 't', 't.id = g.object_type_id');


        if ($user && $collectionId === null) {
            $qb->addSelect('gc.geo_object_id as entry');
            $qb->leftJoin('t', '(x_survey.gc_collection_content gc
                INNER JOIN x_survey.gc_collection c ON (gc.geo_collection_id = c.id
                                 AND c.user_id = :user_id
                                 ))', '', 'gc.geo_object_id = g.id');
        } elseif ($user && $collectionId) {
            /*
            $qb->addSelect('gc.geo_object_id as entry');
            $qb->leftJoin('t', '(x_survey.gc_collection_content gc
                INNER JOIN x_survey.gc_collection c ON (gc.geo_collection_id = c.id
                                 AND c.user_id = :user_id
                                 AND c.uuid = :collection_id))', '', 'gc.geo_object_id = g.id');
            */
        }

        $queryParts[] = $qb->getSQL();

        $sql = implode(' ', $queryParts);

        $stmt = $conn->prepare($sql);

        $stmt->bindValue('x_min', $this->utils->bbox($in, 0));
        $stmt->bindValue('y_min', $this->utils->bbox($in, 1));
        $stmt->bindValue('x_max', $this->utils->bbox($in, 2));
        $stmt->bindValue('y_max', $this->utils->bbox($in, 3));
        $stmt->bindValue('zoom', $zoom);
        $stmt->bindValue('simplify_tolerance', $simplifyTolerance);


        if ($user && $collectionId === null) {
            $stmt->bindValue('user_id', $user->getId());
        } elseif ($user && $collectionId) {
            $stmt->bindValue('user_id', $user->getId());
         #   $stmt->bindValue('collection_id', $collectionId);
        }

        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    public function userSubmitted(int $userId, float $simplifyTolerance): \Generator
    {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            SELECT
                g.id,
                g.uuid,
                g.style_base,
                g.style_hover,
                g.name as geo_name,
                t.name as type_name,
                g.attributes,
                ST_AsGeoJSON(ST_Simplify(gb.coordinates::geometry, :simplify_tolerance, true)) AS geometry,
                jsonb_build_object(
                    \'urp\', uc.is_completed::int
                ) as attributes
            FROM
                x_survey.result_user_completion uc
                    INNER JOIN
                x_geospatial.geo_object g ON uc.geo_object_id = g.id
                    INNER JOIN
                x_geometry.geometry_base gb ON g.id = gb.geo_object_id
                    INNER JOIN
                x_geospatial.object_type t ON g.object_type_id = t.id
            WHERE
                user_id = :user_id
        ');

        $stmt->bindValue('simplify_tolerance', $simplifyTolerance);
        $stmt->bindValue('user_id', $userId);
        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    public function userGeoCollection(int $userId, float $simplifyTolerance): \Generator
    {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            SELECT
                g.id,
                g.uuid,
                g.style_base,
                g.style_hover,
                g.name as geo_name,
                t.name as type_name,
                g.attributes,
                c.uuid as geo_collection_uuid,     
                ST_AsGeoJSON(ST_Simplify(gb.coordinates::geometry, :simplify_tolerance, true)) AS geometry,
                jsonb_build_object(
                    \'gc\', 1
                ) as attributes
            FROM
                x_survey.gc_collection c
                    INNER JOIN
                x_survey.gc_collection_content cc ON c.id = cc.geo_collection_id
                    INNER JOIN
                x_geospatial.geo_object g ON cc.geo_object_id = g.id
                    INNER JOIN
                x_geometry.geometry_base gb ON g.id = gb.geo_object_id
                    INNER JOIN
                x_geospatial.object_type t ON g.object_type_id = t.id
            WHERE
                user_id = :user_id
        ');

        $stmt->bindValue('simplify_tolerance', $simplifyTolerance);
        $stmt->bindValue('user_id', $userId);
        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    public function userGeoCollectionLinks(int $userId, float $simplifyTolerance): \Generator
    {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            WITH z AS (
                SELECT
                    g.id,
                    gb.coordinates
                FROM
                    x_survey.gc_collection c
                        INNER JOIN
                    x_survey.gc_collection_content cc ON c.id = cc.geo_collection_id
                        INNER JOIN
                    x_geospatial.geo_object g ON cc.geo_object_id = g.id
                        INNER JOIN
                    x_geometry.geometry_base gb ON g.id = gb.geo_object_id
                WHERE
                    user_id = :user_id
            )
            SELECT
                g.id,
                g.uuid,
                g.style_base,
                g.style_hover,
                g.name as geo_name,
                \'\' as type_name,
                g.attributes,
                ST_AsGeoJSON(ST_Simplify(gb.coordinates::geometry, :simplify_tolerance, true)) AS geometry,
                jsonb_build_object(
                    \'gc_edge\', 0
                ) as attributes
            FROM
                x_geospatial.geo_object g
                    INNER JOIN
                x_geometry.geometry_base gb ON g.id = gb.geo_object_id
                    CROSS JOIN z
            WHERE
                st_touches(gb.coordinates::geometry, z.coordinates::geometry)
                AND NOT EXISTS(SELECT * FROM x_survey.gc_collection_content c WHERE c.geo_object_id = gb.geo_object_id)
        ');

        $stmt->bindValue('simplify_tolerance', $simplifyTolerance);
        $stmt->bindValue('user_id', $userId);
        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }
}
