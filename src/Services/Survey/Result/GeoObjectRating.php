<?php

namespace App\Services\Survey\Result;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;

class GeoObjectRating
{
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function update(int $geoObjectId, int $userId): void
    {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        $conn->beginTransaction();

        $stmt = $conn->prepare('
            DELETE FROM 
                x_survey.result_geo_object_rating
            WHERE
                user_id = :user_id
                AND geo_object_id = :geo_object_id
        ');

        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();

        $stmt = $conn->prepare('
            INSERT INTO x_survey.result_geo_object_rating
            (
                criterion_subject_id,
                geo_object_id,
                user_id,
                rating
            ) 
            SELECT
                c.subject_id as criterion_subject_id,
                q.geo_object_id,
                q.user_id,
                SUM(c.value) as rating 
            FROM
                x_survey.response_question q
                    INNER JOIN
                x_survey.response_answer a ON q.id = a.question_id
                    INNER JOIN
                x_survey.ev_criterion_definition c ON c.answer_id = a.answer_id
                    INNER JOIN
                x_survey.result_criterion_completion r ON
                    r.subject_id = c.subject_id
                    AND r.geo_object_id = q.geo_object_id
                    AND r.user_id = q.user_id
            WHERE
                q.is_latest = TRUE
                AND r.is_complete = TRUE
                AND q.geo_object_id = :geo_object_id                    
            GROUP BY
                c.subject_id,
                q.geo_object_id,
                q.user_id
            ORDER BY
                geo_object_id            
            ON CONFLICT (criterion_subject_id, geo_object_id, user_id) DO UPDATE SET
                rating = excluded.rating  
        ');

        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();

        $conn->commit();
    }
}
