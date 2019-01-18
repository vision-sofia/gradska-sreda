<?php


namespace App\Services\Survey\Result;


use Doctrine\ORM\EntityManagerInterface;

class UserCompletion
{
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function update(int $geoObjectId, int $userId):void
    {
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            INSERT INTO x_survey.result_user_completion(
                user_id, 
                geo_object_id, 
                data
            )
            SELECT
                cc.user_id,
                cc.geo_object_id,
                (
                    SELECT
                        to_json(t)
                    FROM (
                        SELECT
                            cast(
                                CASE WHEN (
                                    bool_and(is_complete) = TRUE
                                    AND (COUNT(*) = (
                                        SELECT
                                            COUNT(*)
                                        FROM
                                            x_survey.ev_criterion_subject c
                                                INNER JOIN
                                            x_survey.ev_criterion_subject cc ON c.category_id = cc.category_id
                                        WHERE
                                            c.id = MAX(w.subject_id)
                                        )
                                    ) = TRUE
                                ) = TRUE
                                THEN 1 ELSE 0 END AS INTEGER
                            ) as is_complete
                        FROM
                            x_survey.result_criterion_completion w
                        WHERE
                            w.user_id = cc.user_id
                            AND w.geo_object_id = cc.geo_object_id
                    ) t
                )
            FROM
                x_survey.result_criterion_completion cc
            WHERE
                cc.geo_object_id = :geo_object_id
                AND cc.user_id = :user_id
            GROUP BY
                cc.geo_object_id, cc.user_id
            ON CONFLICT (geo_object_id, user_id) DO UPDATE SET
                data = excluded.data    
        ');

        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();
    }
}