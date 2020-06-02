<?php

namespace App\Services\Survey\CriterionSubject;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class CriterionSubjectMetadata
{
    private EntityManagerInterface $em;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    public function updateMaxPoints(?int $subjectId = null): void
    {
        $conn = $this->em->getConnection();

        try {
            // TODO Implement query builder for WHERE cd.subject_id = :subject_id

            $stmt = $conn->prepare('
                INSERT INTO x_survey.ev_criterion_subject_metadata (
                    criterion_subject_id, 
                    max_points
                )
                WITH z AS
                (
                    SELECT
                        cd.subject_id,
                        MAX(cd.value) as point 
                    FROM
                        x_survey.ev_criterion_definition cd
                            INNER JOIN
                        x_survey.q_answer a ON a.id = cd.answer_id
                            INNER JOIN
                        x_survey.q_question q ON q.id = a.question_id
                    WHERE
                        q.has_multiple_answers = FALSE --AND cd.subject_id = :subject_id
                    GROUP BY
                        q.id, q.has_multiple_answers, cd.subject_id
                
                    UNION ALL
                
                    SELECT
                        cd.subject_id,                       
                        cd.value as point 
                    FROM
                        x_survey.ev_criterion_definition cd
                            INNER JOIN
                        x_survey.q_answer a ON a.id = cd.answer_id
                            INNER JOIN
                        x_survey.q_question q ON q.id = a.question_id
                    WHERE
                        q.has_multiple_answers = TRUE --AND cd.subject_id = :subject_id
                )
                SELECT
                    subject_id, 
                    SUM(point) as max_points
                FROM
                    z
                GROUP BY
                    subject_id
                ON CONFLICT (criterion_subject_id) DO UPDATE SET
                    max_points = excluded.max_points        
            ');

            //$stmt->bindValue('subject_id', $subjectId);
            $stmt->execute();
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    public function sync(): void
    {
        $conn = $this->em->getConnection();

        try {
            // TODO add trigger to ensure metadata is empty object instead of null

            $stmt = $conn->prepare('
                UPDATE 
                    x_survey.ev_criterion_subject 
                SET 
                    metadata = \'{}\' 
                WHERE 
                    metadata IS NULL
            ');

            $stmt->execute();
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
        }

        try {
            // TODO sync all metadata fields in one shot

            $stmt = $conn->prepare('
                UPDATE
                    x_survey.ev_criterion_subject p
                SET
                    metadata = jsonb_set(metadata, \'{"max_points"}\', (
                        SELECT
                            COALESCE(
                                (
                                    SELECT
                                        max_points
                                    FROM
                                        x_survey.ev_criterion_subject_metadata
                                    WHERE
                                        criterion_subject_id = p.id
                                )::VARCHAR,
                                \'null\'
                            )
                    ) :: jsonb, true)
            ');

            $stmt->execute();
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
