<?php

namespace App\Services\Rating;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Generator;

class SurveyCompletionRating
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getRating(): Generator
    {
        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();

        /*
          WITH z AS (
            SELECT
                count(*) filter (where r.is_completed = true)::dec as completed,
                count(*)::dec as total,
                r.user_id,
                r.survey_id,
                p.geo_object_id as polygon
            FROM
                x_survey.result_user_completion r
                    INNER JOIN
                x_geometry.geometry_base gb ON gb.geo_object_id = r.geo_object_id
                    CROSS JOIN
                x_geometry.polygon p
            WHERE
                ST_Intersects(p.coordinates::geometry, gb.coordinates::geometry)
            GROUP BY
                r.user_id, r.survey_id, p.id
          )
          SELECT
              total,
              completed,
              user_id,
              survey_id,
              polygon
          FROM
              z
          */

        $stmt = $conn->prepare('
            WITH z AS (
                SELECT
                    count(*) filter (where is_completed = true)::dec as completed,
                    count(*)::dec as total,
                    username
                FROM
                    x_survey.result_user_completion r
                        INNER JOIN
                    x_main.user_base u ON r.user_id = u.id
                GROUP BY
                    u.id
            )
            SELECT 
                total, 
                completed, 
                ((total/2)+(completed+(completed*0.5)))/2 as score, 
                username
            FROM 
                z 
            ORDER BY 
                score DESC
        ');

        $stmt->execute();

        while ($row = $stmt->fetch()) {
            yield $row;
        }
    }
}
