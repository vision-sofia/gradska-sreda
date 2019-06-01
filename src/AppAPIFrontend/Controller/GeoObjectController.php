<?php

namespace App\AppAPIFrontend\Controller;

use App\AppMain\DTO\QuestionDTO;
use App\AppMain\DTO\ResponseAnswerDTO;
use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey;
use App\Services\ApiFrontend\GeoObjectRating;
use App\Services\Survey\Question;
use App\Services\UploaderHelper;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
 * @Route("/geo/", name="api.geo-object.")
 */
class GeoObjectController extends AbstractController
{
    protected $entityManager;
    protected $eventDispatcher;
    protected $uploaderHelper;
    protected $question;
    protected $geoObjectRating;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        UploaderHelper $uploaderHelper,
        Question $question,
        GeoObjectRating $geoObjectRating
    )
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->uploaderHelper = $uploaderHelper;
        $this->question = $question;
        $this->geoObjectRating = $geoObjectRating;
    }

    /**
     * @Route("{id}", name="details", methods={"GET"})
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id" = "uuid"}})
     */
    public function details(GeoObject $geoObject): Response
    {
        $isAvailableForSurvey = $this->getDoctrine()
            ->getRepository(Survey\Spatial\SurveyGeoObject::class)
            ->isInScope($geoObject);

        if (!$isAvailableForSurvey) {
            return $this->redirectToRoute('app.map');
        }

        return new JsonResponse([
            'geoObject' => [
                'id' => $geoObject->getUuid(),
                'name' => $geoObject->getName(),
                'type' => $geoObject->getType()->getName(),
            ],
            'survey' => $this->surveyResult($geoObject)
        ]);
    }


    private function surveyResult(GeoObject $geoObject): array
    {
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
                    'explanation' => $answer->getExplanation()
                ];
            }

            $survey[] = [
                'id' => $question->getId(),
                'uuid' => $question->getUuid(),
                'title' => $question->getTitle(),
                'isAnswered' => $question->isAnswered(),
                'isCompleted' => $question->isCompleted(),
                'hasMultipleAnswers' => $question->getHasMultipleAnswers(),
                'answers' => $answers
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

        return [
            'questions' => $survey,
            'progress' => $progress
        ];
    }

    /**
     * @Route("{id}/result", name="result")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id" = "uuid"}})
     */
    public function result(GeoObject $geoObject): Response
    {
        return new JsonResponse([
            'geoObject' => [
                'id' => $geoObject->getUuid(),
                'name' => $geoObject->getName(),
                'type' => $geoObject->getType()->getName(),
            ],
            'rating' => $this->geoObjectRating->getOverallRating($geoObject->getId()),
            'respondents' => $this->geoObjectRating->getRatingByUser($geoObject->getId()),
        ]);
    }
}
