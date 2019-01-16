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

    public function update(int $userId, int $geoObjectId):void
    {
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            INSERT INTO x_survey.result_user_completion(user_id, geo_object_id, data)
            SELECT
                wz.user_id,
                wz.geo_object_id,
                (
                    SELECT
                        to_json(t)
                    FROM
                        (
                            SELECT
                                cast(CASE WHEN bool_and(is_complete) = TRUE
                                        THEN 1
                                        ELSE 0
                                    END
                                    AS INTEGER) as is_complete
                            FROM
                                x_survey.result_criterion_completion w
                            WHERE
                                w.user_id = :user_id
                                AND w.geo_object_id = wz.geo_object_id
                        ) t
                )
            FROM
                x_survey.result_criterion_completion wz
            WHERE
                wz.geo_object_id = :geo_object_id                
            GROUP BY
                wz.geo_object_id, wz.user_id         
        ');

        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();
    }
}