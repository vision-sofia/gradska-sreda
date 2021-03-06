<?php

namespace App\AppMain\Controller;

use App\AppMain\DTO\QuestionDTO;
use App\AppMain\DTO\ResponseAnswerDTO;
use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey;
use App\AppMain\Entity\User\User;
use App\AppMain\Entity\User\UserInterface;
use App\Event\GeoObjectSurveyTouch;
use App\Message\RebuildStyleByAnswer;
use App\Message\RebuildStyleByQuestion;
use App\Services\Survey\Question;
use App\Services\Survey\Response\Copy;
use App\Services\Survey\Response\QuestionV3;
use App\Services\Survey\Response\QuestionV3 as QuestionResponseService;
use App\Services\UploaderHelper;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ItemController extends AbstractController
{
    protected EntityManagerInterface $entityManager;
    protected EventDispatcherInterface $eventDispatcher;
    protected UploaderHelper $uploaderHelper;
    protected Question $question;
    protected QuestionResponseService $questionResponseService;
    protected MessageBusInterface $messageBus;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        UploaderHelper $uploaderHelper,
        QuestionResponseService $questionResponseService,
        Question $question,
        MessageBusInterface $messageBus
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->uploaderHelper = $uploaderHelper;
        $this->question = $question;
        $this->questionResponseService = $questionResponseService;
        $this->messageBus = $messageBus;
    }

    /**
     * @return array<string, array>
     */
    private function surveyResult(GeoObject $geoObject): array
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException();
        }

        if ($geoObject->getType() === null) {
            return [];
        }

        $questions = $this->getDoctrine()
            ->getRepository(Survey\Question\Question::class)
            ->findQuestions($user, $geoObject)
        ;

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

        $stmt->bindValue('user_id', $user->getId());
        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, ResponseAnswerDTO::class);

        $responseAnswers = [];

        while ($answer = $stmt->fetch()) {
            /* @var ResponseAnswerDTO $answer */
            $responseAnswers[$answer->getQuestionId()][$answer->getId()] = $answer;
        }

        /** @var QuestionDTO[] $q */
        $q = $this->question->build($questions, $responseAnswers);

        $survey = [];

        foreach ($q as $question) {
            $answers = [];

            foreach ($question->getAnswers() as $answer) {
                $answers[] = [
                    'title' => $answer->getTitle(),
                    'parent' => $answer->getParent(),
                    'uuid' => $answer->getUuid(),
                    'isSelected' => $answer->getIsSelected(),
                    'isFreeAnswer' => $answer->getIsFreeAnswer(),
                    'explanation' => $answer->getExplanation(),
                ];
            }

            $survey[] = [
                'id' => $question->getId(),
                'uuid' => $question->getUuid(),
                'title' => $question->getTitle(),
                'isAnswered' => $question->isAnswered(),
                'isCompleted' => $question->isCompleted(),
                'hasMultipleAnswers' => $question->getHasMultipleAnswers(),
                'answers' => $answers,
            ];
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
                  AND q.is_completed = TRUE
            )
            SELECT total, complete FROM z, d
        ');

        $stmt->bindValue('user_id', $user->getId());
        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->bindValue('geo_object_type_id', $geoObject->getType()->getId());
        $stmt->execute();

        $result = $stmt->fetch();

        $progress = [
            'total' => $result['total'],
            'complete' => $result['complete'],
            'percentage' => round(($result['complete'] / $result['total']) * 100),
        ];

        $stmt = $conn->prepare('
            SELECT
                is_confirmed
            FROM
                x_survey.response_location
            WHERE
                geo_object_id = :geo_object_id
                AND user_id = :user_id

        ');

        $stmt->bindValue('user_id', $user->getId());
        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->execute();

        return [
            'questions' => $survey,
            'progress' => $progress,
            'is_confirmed' => $stmt->fetchColumn(),
        ];
    }

    /**
     * @Route("geo/{id}/q", name="app.geo-object.details.q", methods={"GET"})
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id": "uuid"}})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function qq(GeoObject $geoObject): JsonResponse
    {
        return new JsonResponse([
            //            'geoObject' => [
            //                'id' => $geoObject->getUuid(),
            //                'name' => $geoObject->getName(),
            //                'type' => $geoObject->getType()->getName(),
            //            ],
            //            'survey' => $this->surveyResult($geoObject),
        ]);
    }

    /**
     * @Route("geo/{id}/q", name="app.geo-object.details.qz", methods={"POST"})
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id": "uuid"}})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function q2(Request $request, GeoObject $geoObject, QuestionV3 $questionV3): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if ($geoObject->getType() === null) {
            return $this->json([]);
        }

        $text = $request->request->get('explanation');

        if (isset($text)) {
            /** @var Connection $conn */
            $conn = $this->entityManager->getConnection();

            $stmt = $conn->prepare(
                'UPDATE x_survey.response_answer SET explanation = ?'
            );

            $stmt->execute([$text['text']]);

            return new JsonResponse([
                'geoObject' => [
                    'id' => $geoObject->getUuid(),
                    'name' => $geoObject->getName(),
                    'type' => $geoObject->getType()->getName(),
                ],
                'survey' => $this->surveyResult($geoObject),
            ]);
        }

        $answerUuid = $request->request->get('answer');

        $userId = $user->getId();
        $geoObjectId = $geoObject->getId();

        if ($questionV3->isAnsweredAndMultipleAnswers($answerUuid, $userId, $geoObjectId)) {
            $questionV3->uncheck($answerUuid, $userId, $geoObjectId);
            $questionV3->clearDetached($userId, $geoObjectId);
            $questionV3->clearEmptyQuestions($userId, $geoObjectId);

            $questionV3->mark($userId, $geoObjectId);

            $event = new GeoObjectSurveyTouch($geoObject, $user);
            $this->eventDispatcher->dispatch($event, GeoObjectSurveyTouch::NAME);

            $this->messageBus->dispatch(new RebuildStyleByAnswer($geoObjectId, $answerUuid));

            return new JsonResponse([
                'geoObject' => [
                    'id' => $geoObject->getUuid(),
                    'name' => $geoObject->getName(),
                    'type' => $geoObject->getType()->getName(),
                ],
                'survey' => $this->surveyResult($geoObject),
            ]);
        }

        if ($questionV3->isAnsweredAndSingleAnswer($answerUuid, $userId, $geoObjectId)) {
            return new JsonResponse([
                'geoObject' => [
                    'id' => $geoObject->getUuid(),
                    'name' => $geoObject->getName(),
                    'type' => $geoObject->getType()->getName(),
                ],
                'survey' => $this->surveyResult($geoObject),
            ]);
        }

        $questionV3->clearOut($answerUuid, $userId, $geoObjectId);
        $questionV3->response($request->request->get('answer'), [], $geoObject, $user);

        $questionV3->clearDetached($userId, $geoObjectId);
        $questionV3->mark($userId, $geoObjectId);

        $event = new GeoObjectSurveyTouch($geoObject, $user);
        $this->eventDispatcher->dispatch($event, GeoObjectSurveyTouch::NAME);

        $this->messageBus->dispatch(new RebuildStyleByAnswer($geoObjectId, $answerUuid));

        return new JsonResponse([
            'geoObject' => [
                'id' => $geoObject->getUuid(),
                'name' => $geoObject->getName(),
                'type' => $geoObject->getType()->getName(),
            ],
            'survey' => $this->surveyResult($geoObject),
        ]);
    }

    /**
     * @Route("geo/{id}/clear/{question}", name="app.geo-object.details.clear", methods={"POST"})
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id": "uuid"}})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function clearQuestion(GeoObject $geoObject, string $question): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        // TODO: csrf check
        $this->questionResponseService->clear($question, $user->getId(), $geoObject->getId());

        $event = new GeoObjectSurveyTouch($geoObject, $user);
        $this->eventDispatcher->dispatch($event, GeoObjectSurveyTouch::NAME);

        $this->messageBus->dispatch(new RebuildStyleByQuestion($geoObject->getId(), $question));

        return new JsonResponse([], 200);
    }

    /**
     * @Route("geo/{id}/result", name="app.geo-object.result")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id": "uuid"}})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function result(GeoObject $geoObject, SessionInterface $session): Response
    {
        // TODO: cache

        /** @var Connection $conn */
        $conn = $this->getDoctrine()->getConnection();

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

        $stmt = $conn->prepare('
            SELECT
                cr.name AS criterion_name,
                round(AVG(gr.rating), 2) as rating,
                cr.metadata->\'max_points\' as max_points
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
            SELECT
                u.username AS user_username,
                cr.name AS criterion_name,
                round(AVG(gr.rating), 2) AS rating,
                cr.metadata->\'max_points\' as max_points
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
            $resultByUsers[$question['user_username']][] = $question;
        }

        return $this->render('front/geo-object/result.html.twig', [
            'geoObject' => $geoObject,
            'result' => $result,
            'resultByUsers' => $resultByUsers,
            'rating' => $resultByCriterion,
        ]);
    }

    /**
     * @Route("geo/{id}", name="app.geo-object.details")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id": "uuid"}})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function details(GeoObject $geoObject): Response
    {
        $isAvailableForSurvey = $this->getDoctrine()
            ->getRepository(Survey\Spatial\SurveyGeoObject::class)
            ->isInScope($geoObject)
        ;

        if (!$isAvailableForSurvey) {
            return $this->redirectToRoute('app.map');
        }

        return $this->render('front/geo-object/details.html.twig', [
            'geoObject' => $geoObject,
        ]);
    }

    /**
     * @Route("geo/{id}/copy", name="copy", methods="POST")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id": "uuid"}})
     */
    public function copy(GeoObject $geoObject, Copy $copy): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if ($geoObject->getId() !== null) {
            $copy->proceed($geoObject->getId(), $user->getId());

            $event = new GeoObjectSurveyTouch($geoObject, $user);
            $this->eventDispatcher->dispatch($event, GeoObjectSurveyTouch::NAME);
        }

        return new JsonResponse([
            'geoObject' => [
                'id' => $geoObject->getUuid(),
                'name' => $geoObject->getName(),
                'type' => $geoObject->getType() !== null ? $geoObject->getType()->getName() : 'unknown',
            ],
            'survey' => $this->surveyResult($geoObject),
        ]);
    }
}
