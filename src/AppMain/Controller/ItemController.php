<?php

namespace App\AppMain\Controller;

use App\AppMain\DTO\ResponseAnswerDTO;
use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey;
use App\Event\GeoObjectSurveyTouch;
use App\Services\Survey\Question;
use App\Services\Survey\Response\Compose;
use App\Services\Survey\Response\QuestionV2;
use App\Services\UploaderHelper;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ItemController extends AbstractController
{
    protected $entityManager;
    protected $eventDispatcher;
    protected $uploaderHelper;
    protected $question;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        UploaderHelper $uploaderHelper,
        Question $question
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->uploaderHelper = $uploaderHelper;
        $this->question = $question;
    }

    /**
     * @Route("geo/{id}", name="app.geo-object.details")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id" = "uuid"}})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function details(GeoObject $geoObject, string $mediaDir, SessionInterface $session): Response
    {
        $isAvailableForSurvey = $this->getDoctrine()
            ->getRepository(GeoObject::class)
            ->isAvailableForSurvey($geoObject);

        if (!$isAvailableForSurvey) {
            return $this->redirectToRoute('app.map');
        }

        $questions = $this->getDoctrine()
            ->getRepository(Survey\Question\Question::class)
            ->findQuestions($this->getUser(), $geoObject);

        /** @var Connection $conn */
        $conn = $this->getDoctrine()->getConnection();
        $stmt = $conn->prepare('
                SELECT
                    ra.answer_id as id,
                    ra.explanation,
                    ra.photo,
                    rq.question_id 
                FROM
                    x_survey.response_answer ra
                        INNER JOIN
                    x_survey.response_question rq ON ra.question_id = rq.id
                WHERE
                    rq.geo_object_id = :geo_object_id
                    AND rq.user_id = :user_id
            ');

        $stmt->bindValue('user_id', $this->getUser()->getId());
        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, ResponseAnswerDTO::class);

        $responseAnswers = [];
        /** @var ResponseAnswerDTO $answer [] */
        while ($answer = $stmt->fetch()) {
            if (null !== $answer->getPhoto()) {
                // $file = new File($mediaDir . DIRECTORY_SEPARATOR . $answer->getPhoto());
               //# $answer->setPhoto($file);
              //  dump($answer, $answer->getPhoto());
            }

            $responseAnswers[$answer->getQuestionId()][$answer->getId()] = $answer;
        }

        $questions = $this->question->build($questions, $responseAnswers);

        $stmt = $conn->prepare('
            SELECT
                cr.name AS name, 
                round(AVG(rating), 1) AS rating
            FROM
                x_survey.result_geo_object_rating gr
                     INNER JOIN
                x_survey.ev_criterion_subject cr ON gr.criterion_subject_id = cr.id
            WHERE
                gr.geo_object_id = :geo_object_id
            GROUP BY
                cr.id');

        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->execute();

        $result = [];
        while ($question = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $question;
        }

        /*
        $stmt = $conn->prepare('
            SELECT
                u.username AS user_username,
                cr.name AS criterion_name,
                round(AVG(rating), 2) AS rating
            FROM
                x_survey.result_geo_object_rating gr
                    INNER JOIN
                x_survey.ev_criterion_subject cr ON gr.criterion_subject_id = cr.id
                    INNER JOIN
                x_main.user_base u ON gr.user_id = u.id
            WHERE
                gr.geo_object_id = :geo_object_id

            GROUP BY
                cr.id, u.id
            ORDER BY u.username
        ');

        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->execute();

        $resultByUsers = [];
        while ($question = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $resultByUsers[] = $question;
        }
        */

        $stmt = $conn->prepare('
            SELECT
                cr.name AS criterion_name,
                round(AVG(rating), 2) as rating,
                metadata->\'max_points\' as max_points
            FROM
                x_survey.result_geo_object_rating gr
                    INNER JOIN
                x_survey.ev_criterion_subject cr ON gr.criterion_subject_id = cr.id
            WHERE
                gr.geo_object_id = :geo_object_id
            GROUP BY
                cr.id               
        ');

        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->execute();

        $resultByCriterion = [];
        while ($question = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $resultByCriterion[] = $question;
        }

        $stmt = $conn->prepare('
            WITH z as (
                SELECT
                    COUNT(*) as total
                FROM
                    x_survey.geo_object_question gq
                WHERE
                    gq.geo_object_type_id = :geo_object_type_id
                    AND gq.survey_is_active = TRUE
            ), d as (
                SELECT
                    COUNT(*) as complete
                FROM
                    x_survey.response_question q
                WHERE
                  q.geo_object_id = :geo_object_id
                  AND q.user_id = :user_id
            )
            SELECT total, complete FROM z, d        
        ');

        $stmt->bindValue('user_id', $this->getUser()->getId());
        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->bindValue('geo_object_type_id', $geoObject->getType()->getId());
        $stmt->execute();

        $result = $stmt->fetch();

        $progress = [
            'total' => $result['total'],
            'complete' => $result['complete'],
            'percentage' => round(($result['complete'] / $result['total']) * 100),
        ];

        return $this->render('front/geo-object/details.html.twig', [
            'geo_object' => $geoObject,
            'is_available_for_survey' => $isAvailableForSurvey,
            'result' => $result,
          //  'resultByUsers' => $resultByUsers,
            'questions' => $questions,
            'progress' => $progress,
            'rating' => $resultByCriterion,
        ]);
    }

    /**
     * @Route("geo/{id}/result", name="app.geo-object.result", methods="POST")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id" = "uuid"}})
     */
    public function result(Request $request, GeoObject $geoObject, QuestionV2 $question, Compose $compose): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app.login');
        }

        /** @var \Doctrine\DBAL\Connection $conn */
        $conn = $this->getDoctrine()->getConnection();
        $stmt = $conn->prepare('
                SELECT
                    a.uuid as answer_uuid,
                    q.id as question_id,
                    q.uuid as question_uuid
                FROM
                    x_survey.response_answer ra
                        INNER JOIN
                    x_survey.response_question rq ON ra.question_id = rq.id
                        INNER JOIN
                    x_survey.q_answer a ON a.id = ra.answer_id                        
                        INNER JOIN
                    x_survey.q_question q ON q.id = a.question_id
                WHERE
                    rq.geo_object_id = :geo_object_id
                    AND rq.is_latest = TRUE
                    AND rq.user_id = :user_id
            ');

        $stmt->bindValue('user_id', $this->getUser()->getId());
        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->execute();

        $currentAnswers = [];
        $questionHashMap = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $currentAnswers[$row['question_uuid']][] = $row['answer_uuid'];
            $questionHashMap[$row['question_uuid']] = $row['question_id'];
        }

        $answers = $request->get('answers');
        $files = $request->files->get('answers');

        $result = $compose->build($answers, $files, $currentAnswers);

        foreach ($result['new'] as $answerUuid => $answerExtra) {
            $question->response($answerUuid, $answerExtra, $geoObject, $this->getUser());
        }

        // old
        $stmt = $conn->prepare('
            DELETE FROM
                x_survey.response_answer ra
            USING
                x_survey.response_question rq,
                x_survey.q_answer a
            WHERE
                ra.question_id = rq.id
                AND ra.answer_id = a.id
                AND rq.user_id = :user_id
                AND rq.geo_object_id = :geo_object_id
                AND a.uuid = :answer_uuid
        ');

        $stmt->bindValue('user_id', $this->getUser()->getId());
        $stmt->bindValue('geo_object_id', $geoObject->getId());

        foreach ($result['old'] as $item) {
            $stmt->bindValue('answer_uuid', $item);
            $stmt->execute();
        }

        // Update explanation
        if (isset($answers['explanation']) && \is_array($answers['explanation'])) {
            $stmt = $conn->prepare('
                UPDATE
                    x_survey.response_answer ra
                SET
                    explanation = :explanation
                FROM
                    x_survey.q_answer a,
                    x_survey.response_question rq
                WHERE
                    rq.id = ra.question_id
                    AND ra.answer_id = a.id 
                    AND a.uuid = :answer_uuid
                    AND rq.user_id = :user_id
                    AND rq.geo_object_id = :geo_object_id
                  
            ');

            foreach ($answers['explanation'] as $key => $item) {
                if (isset($r[$key])) {
                    $stmt->bindValue('explanation', $item);
                    $stmt->bindValue('answer_uuid', $key);
                    $stmt->bindValue('user_id', $this->getUser()->getId());
                    $stmt->bindValue('geo_object_id', $geoObject->getId());
                    $stmt->execute();
                }
            }
        }

        foreach ($result['all'] as $key => $value) {
            if (isset($value['photo'])) {
                $photo = $value['photo'];

                $file = $this->uploaderHelper->uploadAnswerImage($photo);

                $stmt = $conn->prepare('
                    UPDATE
                        x_survey.response_answer ra
                    SET
                        photo = :photo
                    FROM
                        x_survey.q_answer a,
                        x_survey.response_question rq
                    WHERE
                        rq.id = ra.question_id
                        AND ra.answer_id = a.id 
                        AND a.uuid = :answer_uuid
                        AND rq.user_id = :user_id
                        AND rq.geo_object_id = :geo_object_id
                      
                ');

                $stmt->bindValue('photo', $file);
                $stmt->bindValue('answer_uuid', $key);
                $stmt->bindValue('user_id', $this->getUser()->getId());
                $stmt->bindValue('geo_object_id', $geoObject->getId());
                $stmt->execute();
            }
        }

        foreach ($result['clean'] as $value) {
            if (!isset($questionHashMap[$value])) {
                continue;
            }

            $stmt = $conn->prepare('
                DELETE
                FROM
                    x_survey.response_question
                WHERE
                    question_id = :question_id
                    AND user_id = :user_id
                    AND geo_object_id = :geo_object_id
            ');

            $stmt->bindValue('question_id', $questionHashMap[$value]);
            $stmt->bindValue('user_id', $this->getUser()->getId());
            $stmt->bindValue('geo_object_id', $geoObject->getId());
            $stmt->execute();
        }

        // Remove answers with no parent
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

        $stmt->bindValue('user_id', $this->getUser()->getId());
        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->execute();

        $event = new GeoObjectSurveyTouch($geoObject, $this->getUser());
        $this->eventDispatcher->dispatch(GeoObjectSurveyTouch::NAME, $event);

        return $this->redirectToRoute('app.geo-object.details', [
            'id' => $geoObject->getUuid(),
        ]);
    }
}
