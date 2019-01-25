<?php


namespace App\Services\Survey\Response;

use App\AppMain\Entity\Survey;
use Doctrine\ORM\EntityManagerInterface;

class Question
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function isValidAnswer(Survey\Question\Question $question, Survey\Question\Answer $answer): ?bool
    {
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('
            SELECT EXISTS(
                SELECT
                    *
                FROM
                    x_survey.q_answer
                WHERE
                    id = :answer_id
                    AND  question_id = :question_id
            )                          
        ');

        $stmt->bindValue('answer_id', $answer->getId());
        $stmt->bindValue('question_id', $question->getId());
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}