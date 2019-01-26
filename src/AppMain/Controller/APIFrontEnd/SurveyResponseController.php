<?php

namespace App\AppMain\Controller\APIFrontEnd;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey;
use App\AppMain\Entity\Survey\Question\Answer;
use App\Event\GeoObjectSurveyTouch;
use App\Services\Survey\Response\Question as SurveyResponseQuestionService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("geo-object", name="geo-object.survey.")
 */
class SurveyResponseController extends AbstractController
{
    protected $entityManager;
    protected $eventDispatcher;
    protected $responseQuestionService;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        SurveyResponseQuestionService $responseQuestionService
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->responseQuestionService = $responseQuestionService;
    }

    /**
     * @Route("/{id}/survey", name="question.load", methods="GET")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id" = "uuid"}})
     */
    public function getAnswer(GeoObject $geoObject): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app.login');
        }

        $isAvailableForSurvey = $this->getDoctrine()
                                     ->getRepository(GeoObject::class)
                                     ->isAvailableForSurvey($geoObject)
        ;

        if ($isAvailableForSurvey) {
            $question = $this->getDoctrine()
                             ->getRepository(Survey\Question\Question::class)
                             ->findNextQuestion(
                                 $this->getUser(),
                                 $geoObject
                             )
            ;
        }

        $answers = [];

        foreach ($question->getAnswers() as $answer) {
            $data = [];
            $data['id'] = $answer->getUuid();
            $data['title'] = $answer->getTitle();
            $data['is_free_answer'] = $answer->getIsFreeAnswer();

            if ($answer->getParent() instanceof Answer) {
                $data['parent'] = $answer->getParent()->getUuid();
            } else {
                $data['parent'] = null;
            }

            $answers[] = $data;
        }

        $response = [
            'question'             => $question->getTitle(),
            'has_multiple_answers' => $question->getHasMultipleAnswers(),
            'answers'              => $answers,
        ];

        return new JsonResponse($response);
    }

    public function ans(Answer $answer): array
    {
        return [
            'uuid'  => $answer->getUuid(),
            'title' => $answer->getTitle(),
        ];
    }

    /**
     * @Route("/{id}/survey", name="question.response", methods="POST")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id" = "uuid"}})
     */
    public function result(Request $request, GeoObject $geoObject): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app.login');
        }

        $parent = $request->get('parent');
        $answer = $this->getDoctrine()->getRepository(Answer::class)->findOneBy([
            'uuid' => $parent,
        ])
        ;

        $child = $request->get('child');

        $childAnswers = [];

        if (!empty($child)) {
            foreach ($child as $item) {
                $answer = $this->getDoctrine()->getRepository(Answer::class)->findOneBy([
                    'uuid' => $item,
                ])
                ;

                $childAnswers[] = $answer;
            }
        }


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


        $stmt->bindValue('user_id', $this->getUser()->getId());
        $stmt->bindValue('question_id', $answer->getQuestion()->getId());
        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->execute();

        $location = $this->getDoctrine()
                         ->getRepository(Survey\Response\Location::class)
                         ->findOneBy([
                             'geoObject'   => $geoObject,
                             'user'        => $this->getUser(),
                             'coordinates' => null,
                         ])
        ;

        if ($location === null) {
            $location = new Survey\Response\Location();
            $location->setGeoObject($geoObject);
            $location->setUser($this->getUser());
        }

        $responseQuestion = new Survey\Response\Question();
        $responseQuestion->setUser($this->getUser());
        $responseQuestion->setGeoObject($geoObject);
        $responseQuestion->setQuestion($answer->getQuestion());
        $responseQuestion->setIsLatest(true);
        $responseQuestion->setLocation($location);

        $responseAnswer = new Survey\Response\Answer();
        $responseAnswer->setAnswer($answer);

        $responseQuestion->addAnswer($responseAnswer);

        $this->entityManager->persist($responseQuestion);
        $this->entityManager->flush();

        $event = new GeoObjectSurveyTouch($geoObject, $this->getUser());
        $this->eventDispatcher->dispatch(GeoObjectSurveyTouch::NAME, $event);

        return $this->redirectToRoute('app.geo-object.details', [
            'id' => $geoObject->getUuid(),
        ]);
    }
}
