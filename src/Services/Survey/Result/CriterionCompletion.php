<?php


namespace App\Services\Survey\Result;


use Doctrine\ORM\EntityManagerInterface;

class CriterionCompletion
{
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function update(int $geoObjectId):void
    {
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            INSERT INTO x_survey.result_criterion_completion (subject_id, user_id, geo_object_id, is_complete)
            SELECT
                subject_id, user_id, geo_object_id, (SUM(a) = m.count)
            FROM
                 (
                     (
                        WITH s AS (
                            SELECT
                                f.question_id, q.user_id, q.geo_object_id
                            FROM
                                x_survey.flow f
                                    INNER JOIN
                                x_survey.response_answer a ON a.answer_id = f.answer_id
                                    INNER JOIN
                                x_survey.ev_criterion_definition ca ON ca.answer_id = a.answer_id
                                    INNER JOIN
                                x_survey.response_question q ON a.question_id = q.id
                            WHERE
                                q.geo_object_id = :geo_object_id
                        )
            
                        SELECT
                            COUNT(*) as a, q.subject_id, s.user_id, s.geo_object_id
                        FROM
                            x_survey.question_subject q
                                INNER JOIN
                            s ON s.question_id = q.question_id
                        GROUP BY
                            q.subject_id, s.user_id, s.geo_object_id
                    )
                    UNION ALL
                    (
                        WITH z AS (
                            SELECT
                               COUNT(*) as a, cd.subject_id, rq.user_id, rq.geo_object_id
                            FROM
                                x_survey.q_answer a
                                    INNER JOIN
                                x_survey.response_question rq ON rq.question_id = a.question_id
                                    INNER JOIN
                                x_survey.ev_criterion_definition cd ON a.id = cd.answer_id
                            WHERE
                                rq.geo_object_id = :geo_object_id
                            GROUP BY
                                cd.subject_id, rq.id
                        )
                        SELECT
                            COUNT(*),
                            z.subject_id,
                            z.user_id,
                            z.geo_object_id
                        FROM
                            z
                                INNER JOIN
                            x_survey.ev_criterion_subject cs ON subject_id = cs.id
                        GROUP BY
                            cs.id,
                            z.subject_id,
                            z.user_id,
                            z.geo_object_id
                    )
                ) as re
                    INNER JOIN
                x_survey.ev_criteria_metadata m ON m.s_id = subject_id
            GROUP BY
                subject_id,
                user_id,
                geo_object_id,
                m.count
            ON CONFLICT (subject_id, user_id,  geo_object_id) DO UPDATE SET
                is_complete = excluded.is_complete      
        ');

        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();
    }
}