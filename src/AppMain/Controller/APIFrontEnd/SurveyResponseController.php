<?php

namespace App\AppMain\Controller\APIFrontEnd;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey;
use App\AppMain\Entity\Survey\Question\Answer;
use App\Event\GeoObjectSurveyTouch;
use App\Services\Survey\Response\Question as SurveyResponseQuestionService;
use App\Services\Survey\Response\Question;
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
            return new JsonResponse(['message' => 'Unauthorized'], 401);
        }

        $isAvailableForSurvey = $this->getDoctrine()
                                     ->getRepository(GeoObject::class)
                                     ->isAvailableForSurvey($geoObject)
        ;

        if ($isAvailableForSurvey === false) {
            return new JsonResponse(['message' => 'error'], 400);
        }

        $question = $this->getDoctrine()
                         ->getRepository(Survey\Question\Question::class)
                         ->findNextQuestion(
                             $this->getUser(),
                             $geoObject
                         )
        ;

        if ($question === null) {
            return new JsonResponse(['message' => 'empty question'], 400);
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

    /**
     * @Route("/{id}/survey", name="question.response", methods="POST")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id" = "uuid"}})
     */
    public function result(Request $request, GeoObject $geoObject, Question $question)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app.login');
        }

        $answers = $request->getContent();
        $answers = json_decode($answers, true);

        $question->response($answers['answers'], $geoObject, $this->getUser());

        $event = new GeoObjectSurveyTouch($geoObject, $this->getUser());
        $this->eventDispatcher->dispatch(GeoObjectSurveyTouch::NAME, $event);

        return new JsonResponse([]);
    }
}
