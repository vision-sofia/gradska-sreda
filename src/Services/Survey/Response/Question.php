<?php

namespace App\Services\Survey\Response;

use App\AppMain\Entity\Geospatial\GeoObjectInterface;
use App\AppMain\Entity\Survey;
use App\AppMain\Entity\Survey\Question\Answer;
use App\AppMain\Entity\User\UserInterface;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class Question
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function clear(string $questionUuid, int $userId): void
    {
        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('
            DELETE FROM
                x_survey.response_question r
                    USING
                x_survey.q_question q
            WHERE
                r.question_id = q.id
                AND q.uuid = :question_uuid
                AND user_id = :user_id
        ');

        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('question_uuid', $questionUuid);
        $stmt->execute();
    }

    public function response(array $answers, GeoObjectInterface $geoObject, UserInterface $user)
    {
        $answer = $this->entityManager->getRepository(Answer::class)->findOneBy([
            'uuid' => key($answers),
        ]);

        /** @var Survey\Question\Question $question */
        $question = $answer->getQuestion();

        // Check: Is question are available for this geo-object
        // TODO: Survey scope check (geo-object, question)
        // TODO: Redis cache

        $countAnswers = $question->getAnswers()->count();

        // Check: Is number of input answers fit in number of question answers
        if (\count($answers) > $countAnswers) {
            return new JsonResponse(['error']);
        }

        // Check: Is all input answers are from one questions
        // TODO: WHERE id IN (:answers) GROUP BY question_ID
        // TODO: Redis cache

        // Check 4: Is single answer question have one input answer

        // BEFORE INSERT trigger simulation
        $conn = $this->entityManager->getConnection();
        $stmt = $conn->prepare('
            UPDATE
                x_survey.response_question
            SET
                is_latest = FALSE
            WHERE
                user_id = :user_id
                AND question_id = :question_id
                AND geo_object_id = :geo_object_id
                AND is_latest = TRUE
        ');

        $stmt->bindValue('user_id', $user->getId());
        $stmt->bindValue('question_id', $answer->getQuestion()->getId());
        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->execute();

        $location = $this->entityManager
            ->getRepository(Survey\Response\Location::class)
            ->findOneBy([
                'geoObject' => $geoObject,
                'user' => $user,
                'coordinates' => null,
            ])
        ;

        if (null === $location) {
            $location = new Survey\Response\Location();
            $location->setGeoObject($geoObject);
            $location->setUser($user);
        }

        $responseQuestion = $this->entityManager->getRepository(Survey\Response\Question::class)
            ->findOneBy([
                'user' => $user,
                'geoObject' => $geoObject,
                'question' => $answer->getQuestion(),
            ])
        ;

        if ($responseQuestion === null) {
            $responseQuestion = new Survey\Response\Question();
            $responseQuestion->setUser($user);
            $responseQuestion->setGeoObject($geoObject);
            $responseQuestion->setQuestion($answer->getQuestion());
            $responseQuestion->setIsLatest(true);
            $responseQuestion->setLocation($location);
        }

        foreach ($answers as $answerUuid => $answer) {
            $a = $this->entityManager->getRepository(Answer::class)->findOneBy([
                'uuid' => $answerUuid,
            ]);

            $responseAnswer = new Survey\Response\Answer();
            $responseAnswer->setAnswer($a);

            $responseQuestion->addAnswer($responseAnswer);

            $this->entityManager->persist($responseQuestion);
        }

        $this->entityManager->flush();
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
