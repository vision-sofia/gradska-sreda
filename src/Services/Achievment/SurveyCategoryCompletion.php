<?php

namespace App\Services\Achievment;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SurveyCategoryCompletion
{
    protected EntityManagerInterface $em;
    protected LoggerInterface $logger;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function updateUserById(int $userId): void
    {
        $conn = $this->em->getConnection();

        try {
            $stmt = $conn->prepare('
                SELECT
                    uc.user_id,
                    a.id as achievement_id,
                    COUNT(*) as count,
                    a.threshold,
                    COUNT(*) >= a.threshold as is_completed
                FROM
                    x_geospatial.geo_object g
                        INNER JOIN
                    x_survey.survey_element e ON e.object_type_id = g.object_type_id
                        INNER JOIN
                    x_survey.result_user_completion uc ON g.id = uc.geo_object_id
                        INNER JOIN
                    x_main.a_survey_completion_achievement a ON a.survey_category_id = e.category_id
                WHERE
                    uc.is_completed = TRUE
                    AND uc.user_id = :user_id
                GROUP BY
                    a.id, uc.user_id
            ');

            $stmt->bindValue('user_id', $userId);
            $stmt->execute();
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
