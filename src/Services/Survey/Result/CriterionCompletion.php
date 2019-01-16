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
            INSERT INTO x_survey.result_criterion_completion
            (
                subject_id,
                user_id,
                geo_object_id,
                is_complete
            )
            SELECT
                re.subject_id,
                re.user_id,
                re.geo_object_id,
                (SUM(a) = m.count) as is_complete
            FROM
                 (
                     (
                        WITH sk AS (
                            SELECT
                                f.question_id, rq.user_id, rq.geo_object_id
                            FROM
                                x_survey.survey_flow f
                                    INNER JOIN
                                x_survey.response_answer ra ON ra.answer_id = f.answer_id
                                    INNER JOIN
                                x_survey.ev_criterion_definition ca ON ca.answer_id = ra.answer_id
                                    INNER JOIN
                                x_survey.response_question rq ON ra.question_id = rq.id
                        )
                        SELECT
                            COUNT(*) as a,
                            q.subject_id,
                            sk.user_id,
                            sk.geo_object_id
                        FROM
                            x_survey.ev_criterion_subject_question q
                                INNER JOIN
                            sk ON sk.question_id = q.question_id
                        GROUP BY
                            q.subject_id, sk.user_id, sk.geo_object_id
                    )
                    UNION ALL
                    (
                        WITH an AS (
                            SELECT
                               COUNT(*) as a, cd.subject_id, rq.user_id, rq.geo_object_id
                            FROM
                                x_survey.q_answer a
                                    INNER JOIN
                                x_survey.response_question rq ON rq.question_id = a.question_id
                                    INNER JOIN
                                x_survey.ev_criterion_definition cd ON a.id = cd.answer_id
                            GROUP BY
                                cd.subject_id, rq.id
                        )
                        SELECT
                            COUNT(*),
                            an.subject_id,
                            an.user_id,
                            an.geo_object_id
                        FROM
                            an
                                INNER JOIN
                            x_survey.ev_criterion_subject cs ON subject_id = cs.id
                        GROUP BY
                            cs.id,
                            an.subject_id,
                            an.user_id,
                            an.geo_object_id
                    )
                ) as re
                    INNER JOIN
                (SELECT subject_id, COUNT(*) FROM x_survey.ev_criterion_question GROUP BY subject_id) m ON m.subject_id = re.subject_id
            GROUP BY
                re.subject_id,
                re.user_id,
                re.geo_object_id,
                m.count
            ON CONFLICT (subject_id, user_id,  geo_object_id) DO UPDATE SET
                is_complete = excluded.is_complete             
        ');

        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();
    }
}