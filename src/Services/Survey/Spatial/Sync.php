<?php

namespace App\Services\Survey\Spatial;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;

class Sync
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function syncGeoObjects(): void
    {
        $conn = $this->entityManager->getConnection();
        $conn->beginTransaction();

        try {
            # Insert survey objects
            $stmt = $conn->prepare('
                INSERT INTO x_survey.spatial_geo_object (
                    geo_object_id,
                    uuid,
                    geo_object_name,
                    object_type_id,
                    object_type_name,
                    survey_id,
                    properties
                )
                SELECT
                    g.id,
                    g.uuid,
                    g.name,
                    t.id,
                    t.name,
                    c.survey_id,
                    jsonb_strip_nulls(jsonb_build_object(
                        \'_sca\', c.name,
                        \'_behavior\', \'survey\'
                    )) as properties
                FROM
                    x_geospatial.geo_object g
                        INNER JOIN
                    x_survey.survey_element e ON g.object_type_id = e.object_type_id
                        INNER JOIN
                    x_survey.survey_category c ON e.category_id = c.id
                        INNER JOIN
                    x_geospatial.object_type t ON g.object_type_id = t.id
                ON CONFLICT (geo_object_id, survey_id) 
                DO UPDATE SET
                    geo_object_id = excluded.geo_object_id,
                    uuid = excluded.uuid,
                    geo_object_name = excluded.geo_object_name,
                    object_type_id = excluded.object_type_id,
                    object_type_name = excluded.object_type_name,
                    survey_id = excluded.survey_id,
                    properties = excluded.properties
            ');

            $stmt->execute();

            # Insert auxiliary objects
            $stmt = $conn->prepare('
                INSERT INTO x_survey.spatial_geo_object (
                    geo_object_id,
                    uuid,
                    geo_object_name,
                    object_type_id,
                    object_type_name,
                    survey_id,
                    properties
                )
                SELECT
                    g.id,
                    g.uuid,
                    g.name,
                    t.id,
                    t.name,
                    a.survey_id,
                    jsonb_strip_nulls(jsonb_build_object(
                        \'_behavior\', a.behavior,
                        \'_tc\', g.local_properties->\'_tc\',
                        \'has_vhc_other\', g.local_properties->\'has_vhc_other\',
                        \'has_vhc_metro\', g.local_properties->\'has_vhc_metro\'
                    )) as properties
                FROM
                    x_geospatial.geo_object g
                        INNER JOIN
                    x_survey.survey_auxiliary_object_type a ON g.object_type_id = a.object_type_id
                        INNER JOIN
                    x_geospatial.object_type t ON g.object_type_id = t.id
                ON CONFLICT (geo_object_id, survey_id) 
                DO UPDATE SET
                    geo_object_id = excluded.geo_object_id,
                    uuid = excluded.uuid,
                    geo_object_name = excluded.geo_object_name,
                    object_type_id = excluded.object_type_id,
                    object_type_name = excluded.object_type_name,
                    survey_id = excluded.survey_id,
                    properties = excluded.properties
            ');

            $stmt->execute();
            $conn->commit();
        } catch (DBALException $e) {
            throw $e;
        }
    }
}
