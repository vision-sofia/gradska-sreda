<?php

namespace App\AppMain\Repository\Survey;

use App\AppMain\Entity\Survey\Survey\Survey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\Persistence\ManagerRegistry;

class SurveyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Survey::class);
    }

    public function findSurveyIdByAnswerUuid(string $answerUuid): ?int
    {
        /** @var Connection $conn */
        $conn = $this->_em->getConnection();

        $stmt = $conn->prepare('
            SELECT
                s.id
            FROM
                x_survey.survey s
                    INNER JOIN
                x_survey.survey_category c ON s.id = c.survey_id
                    INNER JOIN
                x_survey.q_question q ON c.id = q.category_id
                    INNER JOIN
                x_survey.q_answer a ON q.id = a.question_id
            WHERE
                a.uuid = ?
        ');

        $stmt->execute([$answerUuid]);

        return $stmt->fetchColumn();
    }

    public function findSurveyIdByQuestionUuid(string $questionUuid): ?int
    {
        /** @var Connection $conn */
        $conn = $this->_em->getConnection();

        $stmt = $conn->prepare('
            SELECT
                s.id
            FROM
                x_survey.survey s
                    INNER JOIN
                x_survey.survey_category c ON s.id = c.survey_id
                    INNER JOIN
                x_survey.q_question q ON c.id = q.category_id
            WHERE
                q.uuid = ?
        ');

        $stmt->execute([$questionUuid]);

        return $stmt->fetchColumn();
    }
}
