<?php

namespace App\Services\Geospatial;

use App\AppMain\Entity\User\UserInterface;
use App\Services\Geometry\Utils;
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
                            AND m.coordinates && ST_MakeEnvelope(:x_min, :y_min, :x_max, :y_max)
                            AND :zoom <= min_zoom AND :zoom > max_zoom

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
                        WHERE
                            m.coordinates && ST_MakeEnvelope(:x_min, :y_min, :x_max, :y_max)
                            AND :zoom <= min_zoom AND :zoom > max_zoom
                    ) as w
            )
            SELECT
                g.id,
                g.uuid,
                g.name as geo_name,
                t.name as type_name,
                g.attributes,
                g.geometry,
                gc.geo_object_id as entry
            FROM
                g
                    INNER JOIN
                x_geospatial.object_type t ON t.id = g.object_type_id
                    LEFT JOIN
                x_survey.gc_collection_content gc
                    LEFT JOIN
                x_survey.gc_collection c
                    ON gc.geo_collection_id = c.id
                    ON gc.geo_object_id = g.id
               --     AND c.user_id = :user_id
               --     AND c.uuid = :collection_id
        ');

        $stmt->bindValue('x_min', $this->utils->bbox($in, 0));
        $stmt->bindValue('y_min', $this->utils->bbox($in, 1));
        $stmt->bindValue('x_max', $this->utils->bbox($in, 2));
        $stmt->bindValue('y_max', $this->utils->bbox($in, 3));
        $stmt->bindValue('zoom', $zoom);
        $stmt->bindValue('simplify_tolerance', $simplifyTolerance);
        //$stmt->bindValue('collection_id', $collectionId);

        if ($user) {
            // $stmt->bindValue('user_id', $this->getUser()->getId());
        }
        //   $stmt->bindValue('user_id', null);

        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }
}
