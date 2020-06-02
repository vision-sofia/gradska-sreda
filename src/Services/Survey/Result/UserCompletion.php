<?php

namespace App\Services\Survey\Result;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;

class UserCompletion
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    // TODO: Remove hardcoded values, make it dynamic
    public function updateHybrid(int $geoObjectId): void
    {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            SELECT
                properties,
                EXISTS(
                    SELECT
                        *
                    FROM
                        x_survey.result_user_completion c
                    WHERE
                        c.is_completed = true
                        AND c.geo_object_id = s.geo_object_id
                ) as has_completion
            FROM
                x_survey.spatial_geo_object s
            WHERE
                s.geo_object_id = :geo_object_id
        ');

        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();

        $data = $stmt->fetch();
        $properties = json_decode($data['properties'], true);

        $styleValue = '';

        if ($properties['_sca'] === 'Пешеходни отсечки' && $data['has_completion'] === true) {
            $styleValue = 't';
        } elseif ($properties['_sca'] === 'Алеи' && $data['has_completion'] === true) {
            $styleValue = 'a';
        } elseif ($properties['_sca'] === 'Пресичания' && $data['has_completion'] === true) {
            $styleValue = 'p';
        }

        $stmt = $conn->prepare('
            UPDATE
                x_survey.spatial_geo_object
            SET
                properties = properties || :hc
            WHERE
                geo_object_id = :geo_object_id
        ');

        $stmt->bindValue('hc', json_encode(['_hc' => $styleValue]));
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();
    }

    public function update(int $geoObjectId, int $userId): void
    {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        $conn->beginTransaction();

        $stmt = $conn->prepare('
            DELETE FROM
                x_survey.result_user_completion
            WHERE
                user_id = :user_id
                AND geo_object_id = :geo_object_id
        ');

        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();

        $stmt = $conn->prepare('
            INSERT INTO x_survey.result_user_completion(
                user_id,
                geo_object_id,
                survey_id,
                is_completed
            )
            SELECT
                cc_s.user_id,
                cc_s.geo_object_id,
                sc_s.survey_id,
                (bool_and(is_complete) = TRUE
                    AND (COUNT(*) = (
                        SELECT
                            COUNT(*)
                        FROM
                            x_survey.ev_criterion_subject cs_c
                                INNER JOIN
                            x_survey.ev_criterion_subject cs2_c ON cs_c.category_id = cs2_c.category_id
                        WHERE
                            cs_c.id = MAX(cc_s.subject_id)
                            AND EXISTS(SELECT * FROM x_survey.ev_criterion_definition WHERE subject_id = cs2_c.id)
                        )
                    ) = TRUE
                ) as is_complete
            FROM
                x_survey.result_criterion_completion cc_s
                    INNER JOIN
                x_survey.ev_criterion_subject cs_s ON cc_s.subject_id = cs_s.id
                    INNER JOIN
                x_survey.survey_category sc_s ON cs_s.category_id = sc_s.id
            WHERE
                cc_s.user_id = :user_id
                AND cc_s.geo_object_id = :geo_object_id
            GROUP BY
                cc_s.user_id,
                cc_s.geo_object_id,
                sc_s.survey_id
            ON CONFLICT (user_id, geo_object_id, survey_id) DO UPDATE SET
                is_completed = excluded.is_completed
        ');

        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();

        $conn->commit();

        $this->updateHybrid($geoObjectId);
    }

    // @deprecated
    /*
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
                        json_object_agg(survey_id, to_jsonb(t))
                    FROM
                        (
                            SELECT
                                sc_s.survey_id,
                                cast (
                                    CASE WHEN (
                                        bool_and(is_complete) = TRUE
                                        AND (COUNT(*) = (
                                            SELECT
                                                COUNT(*)
                                            FROM
                                                x_survey.ev_criterion_subject cs_c
                                                    INNER JOIN
                                                x_survey.ev_criterion_subject cs2_c ON cs_c.category_id = cs2_c.category_id
                                            WHERE
                                                  cs_c.id = MAX(cc_s.subject_id)
                                            )
                                        ) = TRUE
                                    ) = TRUE
                                    THEN 1 ELSE 0 END AS INTEGER
                                ) as is_complete

                            FROM
                                x_survey.result_criterion_completion cc_s
                                    INNER JOIN
                                x_survey.ev_criterion_subject cs_s ON cc_s.subject_id = cs_s.id
                                    INNER JOIN
                                x_survey.survey_category sc_s ON cs_s.category_id = sc_s.id
                            WHERE
                                cc_s.user_id = cc.user_id
                                AND cc_s.geo_object_id = cc.geo_object_id
                            GROUP BY
                                sc_s.survey_id
                        ) as t
                )
            FROM
                x_survey.result_criterion_completion cc
            WHERE
                cc.user_id = :user_id
                AND cc.geo_object_id = :geo_object_id
            GROUP BY
                cc.user_id,
                cc.geo_object_id
            ON CONFLICT (geo_object_id, user_id) DO UPDATE SET
                data = excluded.data
        ');

        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();
    }
    */
}
