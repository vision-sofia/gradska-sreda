<?php

namespace App\Services\Survey\Response;

use App\AppMain\Entity\Geospatial\GeoObjectInterface;
use App\AppMain\Entity\Survey;
use App\AppMain\Entity\Survey\Question\Answer;
use App\AppMain\Entity\User\UserInterface;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class QuestionV3
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function uncheck(string $answerUuid, int $userId, int $geoObjectId): void
    {
        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('
                DELETE
                FROM
                    x_survey.response_answer r
                        USING
                    x_survey.q_answer a
                        INNER JOIN
                    x_survey.q_question q ON a.question_id = q.id
                        INNER JOIN
                    x_survey.response_question rq ON q.id = rq.question_id
                WHERE
                    r.answer_id = a.id
                    AND a.uuid = :answer_uuid
                    AND rq.user_id = :user_id
                    AND rq.geo_object_id = :geo_object_id
                    AND (a.parent IS NOT NULL OR (a.parent IS NULL AND q.has_multiple_answers = TRUE))
            ');

        $stmt->bindValue('answer_uuid', $answerUuid);
        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();
    }

    public function clear(string $questionUuid, int $userId, int $geoObjectId): void
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
                AND r.geo_object_id = :geo_object_id
        ');

        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('question_uuid', $questionUuid);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();
    }

    public function clearEmptyQuestions(int $userId, int $geoObjectId): void
    {
        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('
            DELETE FROM
                x_survey.response_question q
            WHERE
                NOT EXISTS((SELECT * FROM x_survey.response_answer a WHERE a.question_id = q.id))
                AND q.user_id = :user_id
                AND q.geo_object_id = :geo_object_id
            ');

        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();
    }

    public function mark(int $userId, int $geoObjectId): void
    {
        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('
            WITH z AS (
                SELECT
                    a.id,
                    a.parent,
                    ra.question_id,
                    ra.id as response_answer_id,
                    ra.answer_id,
                    EXISTS((
                        SELECT
                            *
                        FROM
                            x_survey.response_answer sra
                                INNER JOIN
                            x_survey.q_answer sa ON sra.answer_id = sa.id
                        WHERE
                            sra.question_id = ra.question_id
                            AND ra.answer_id = sa.parent
                    )) as w,
                    a.is_child_answer_required
                FROM
                    x_survey.response_answer ra
                        INNER JOIN
                    x_survey.q_answer a ON ra.answer_id = a.id
                        INNER JOIN
                    x_survey.response_question rq ON ra.question_id = rq.id
                WHERE

                    rq.user_id = :user_id
                    AND rq.geo_object_id = :geo_object_id
            )
            UPDATE
                x_survey.response_answer raa
            SET
                is_completed = ((w = TRUE AND is_child_answer_required = TRUE) OR is_child_answer_required = FALSE)
                    FROM
                z
            WHERE
                z.question_id = raa.question_id
                AND raa.id = z.response_answer_id
        ');

        //$stmt->bindValue('answer_uuid', $answerUuid);
        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();

        $stmt = $conn->prepare('
            UPDATE
                x_survey.response_question rq
            SET
                is_completed = (SELECT bool_and(is_completed) FROM x_survey.response_answer ira WHERE ira.question_id = rq.id)
            FROM
                x_survey.response_answer ra
                    INNER JOIN
                x_survey.q_answer a ON ra.answer_id = a.id
            WHERE
                rq.id = ra.question_id
                AND rq.user_id = :user_id
                AND rq.geo_object_id = :geo_object_id
        ');

        //   $stmt->bindValue('answer_uuid', $answerUuid);
        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();
    }

    public function isAnsweredAndMultipleAnswers(string $answerUuid, int $userId, int $geoObjectId): bool
    {
        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('
            SELECT EXISTS((
                SELECT * FROM
                    x_survey.response_answer r
                        INNER JOIN
                    x_survey.q_answer a ON r.answer_id = a.id
                        INNER JOIN
                    x_survey.q_question q ON a.question_id = q.id
                        INNER JOIN
                    x_survey.response_question rq ON r.question_id = rq.id
                WHERE
                    a.uuid = :answer_uuid
                    AND rq.user_id = :user_id
                    AND rq.geo_object_id = :geo_object_id
                AND (a.parent IS NOT NULL OR (a.parent IS NULL AND q.has_multiple_answers = TRUE))
            ))
        ');

        $stmt->bindValue('answer_uuid', $answerUuid);
        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function isAnsweredAndSingleAnswer(string $answerUuid, int $userId, int $geoObjectId): bool
    {
        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('
            SELECT EXISTS ((
                SELECT
                    *
                FROM
                    x_survey.response_answer ra
                        INNER JOIN
                    x_survey.response_question rq ON ra.question_id = rq.id
                        INNER JOIN
                    x_survey.q_answer a ON ra.answer_id = a.id
                        INNER JOIN
                    x_survey.q_question q ON a.question_id = q.id
                WHERE
                    a.uuid = :answer_uuid
                    AND rq.geo_object_id = :geo_object_id
                    AND rq.user_id = :user_id
                    AND a.parent IS NULL
                    AND q.has_multiple_answers = FALSE
            ))
        ');

        $stmt->bindValue('answer_uuid', $answerUuid);
        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function clearOut(string $answerUuid, int $userId, int $geoObjectId): void
    {
        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('
            DELETE
            FROM
                x_survey.response_answer ra
                    USING
                x_survey.response_question rq,
                x_survey.q_answer a
                    INNER JOIN
                x_survey.q_question q ON a.question_id = q.id
            WHERE
                a.uuid = :answer_uuid
                AND a.parent IS NULL
                AND q.has_multiple_answers = FALSE
                AND rq.id = ra.question_id
                AND rq.user_id = :user_id
                AND rq.geo_object_id = :geo_object_id
                AND rq.question_id = q.id
        ');

        $stmt->bindValue('answer_uuid', $answerUuid);
        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();
    }

    /**
     * Remove answers with no parent.
     */
    public function clearDetached(int $userId, int $geoObjectId): void
    {
        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('
            DELETE FROM
                x_survey.response_answer ra1
            WHERE
                ra1.id IN (
                    WITH t AS (
                        SELECT
                            a.parent,
                            a.id as answer_id,
                            ra.id as ra_id
                        FROM
                            x_survey.response_answer ra
                                INNER JOIN
                            x_survey.q_answer a ON ra.answer_id = a.id
                                INNER JOIN
                            x_survey.response_question rq ON ra.question_id = rq.id
                        WHERE
                            rq.geo_object_id = :geo_object_id
                            AND rq.user_id = :user_id
                    )
                    SELECT ra_id FROM t
                    WHERE parent IS NOT NULL AND parent NOT IN ((SELECT answer_id FROM t))
                )
        ');

        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->execute();
    }

    /**
     * @param array<string, mixed> $extra
     */
    public function response(string $answerUuid, array $extra, GeoObjectInterface $geoObject, UserInterface $user): void
    {
        /** @var Answer|null $answer */
        $answer = $this->entityManager->getRepository(Answer::class)->findOneBy([
            'uuid' => $answerUuid,
        ]);

        if ($answer === null || $answer->getQuestion() === null) {
            return;
        }

        /** @var Survey\Question\Question $question */
        $question = $answer->getQuestion();

        // Check: Is question are available for this geo-object
        // TODO: Survey scope check (geo-object, question)
        // TODO: Redis cache

        // $countAnswers = $question->getAnswers()->count();

        // Check: Is number of input answers fit in number of question answers
        // if (\count($answers) > $countAnswers) {
        //     return new JsonResponse(['error']);
        // }

        // Check: Is all input answers are from one questions
        // TODO: WHERE id IN (:answers) GROUP BY question_ID
        // TODO: Redis cache

        // Check 4: Is single answer question have one input answer

        $location = $this->entityManager
            ->getRepository(Survey\Response\Location::class)
            ->findOneBy([
                'geoObject' => $geoObject,
                'user' => $user,
                'coordinates' => null,
            ])
        ;

        if (null === $location) {
            $conn = $this->entityManager->getConnection();
            $stmt = $conn->prepare('
                SELECT
                    survey_id
                FROM
                    x_survey.geo_object_question
                WHERE
                    question_id = ?
            ');

            $stmt->execute([$question->getId()]);

            $surveyId = $stmt->fetchColumn();

            $survey = $this->entityManager->find(Survey\Survey\Survey::class, $surveyId);

            $location = new Survey\Response\Location();
            $location->setGeoObject($geoObject);
            $location->setUser($user);
            $location->setSurvey($survey);
        }

        $responseQuestion = $this->entityManager->getRepository(Survey\Response\Question::class)
            ->findOneBy([
                'user' => $user,
                'geoObject' => $geoObject,
                'question' => $answer->getQuestion(),
            ])
        ;

        if (null === $responseQuestion) {
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
            $stmt->bindValue('question_id', $question->getId());
            $stmt->bindValue('geo_object_id', $geoObject->getId());
            $stmt->execute();

            $responseQuestion = new Survey\Response\Question();
            $responseQuestion->setUser($user);
            $responseQuestion->setGeoObject($geoObject);
            $responseQuestion->setQuestion($answer->getQuestion());
            $responseQuestion->setIsLatest(true);
            $responseQuestion->setLocation($location);

            $responseAnswer = new Survey\Response\Answer();
            $responseAnswer->setAnswer($answer);

            if (isset($extra['explanation'])) {
                $responseAnswer->setExplanation($extra['explanation']);
            }

            if (isset($extra['photo']) && $extra['photo'] instanceof UploadedFile) {
                /** @var UploadedFile $photo */
                $photo = $extra['photo'];
                $responseAnswer->setPhoto($photo->getClientOriginalName());
            }

            $responseQuestion->addAnswer($responseAnswer);

            $this->entityManager->persist($responseQuestion);

        /*   } elseif ($question->getHasMultipleAnswers() === false && $answer->getParent() === null) {

               $z = $this->entityManager->getRepository(Survey\Response\Answer::class)
                   ->findOneBy([
                       'question' => $responseQuestion
                   ]);

               if ($z) {
                   $z->setAnswer($answer);
                   $z->setExplanation('');
                   $z->setPhoto(null);
               }*/
        } else {
            $responseAnswer = new Survey\Response\Answer();
            $responseAnswer->setAnswer($answer);

            if (isset($extra['explanation'])) {
                $responseAnswer->setExplanation($extra['explanation']);
            }

            if (isset($extra['photo']) && $extra['photo'] instanceof UploadedFile) {
                /** @var UploadedFile $photo */
                $photo = $extra['photo'];
                $responseAnswer->setPhoto($photo->getClientOriginalName());
            }

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
