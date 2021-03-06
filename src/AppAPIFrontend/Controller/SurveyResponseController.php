<?php

namespace App\AppAPIFrontend\Controller;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey;
use App\AppMain\Entity\Survey\Question\Answer;
use App\AppMain\Entity\User\UserInterface;
use App\Event\GeoObjectSurveyTouch;
use App\Services\Survey\Response\Question;
use Doctrine\DBAL\Connection;
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
    protected EntityManagerInterface $entityManager;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    // TODO: refactor in to services
    // TODO: caching

    /**
     * @Route("/{id}/survey", name="question.load", methods="GET")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id": "uuid"}})
     */
    public function getAnswer(GeoObject $geoObject): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return new JsonResponse(['message' => 'Unauthorized'], 401);
        }

        $isAvailableForSurvey = $this->getDoctrine()
            ->getRepository(Survey\Spatial\SurveyGeoObject::class)
            ->isInScope($geoObject)
        ;

        if (false === $isAvailableForSurvey) {
            return new JsonResponse(['message' => 'error'], 400);
        }

        $question = $this->getDoctrine()
            ->getRepository(Survey\Question\Question::class)
            ->findNextQuestion($this->getUser(), $geoObject)
        ;

        if (null === $question) {
            return new JsonResponse([
                'status' => 'no_question',
                'message' => 'Няма повече въпроси',
            ], 200);
        }

        $answers = [];

        foreach ($question->getAnswers() as $answer) {
            $data = [];
            $data['id'] = $answer->getUuid();
            $data['title'] = $answer->getTitle();
            $data['is_free_answer'] = $answer->getIsFreeAnswer();
            $data['is_photo_enabled'] = $answer->getIsPhotoEnabled();

            if ($answer->getParent() instanceof Answer) {
                $data['parent'] = $answer->getParent()->getUuid();
            } else {
                $data['parent'] = null;
            }

            $answers[] = $data;
        }

        $response = [
            'question' => $question->getTitle(),
            'has_multiple_answers' => $question->getHasMultipleAnswers(),
            'answers' => $answers,
        ];

        return new JsonResponse($response);
    }

    /**
     * @Route("/{id}/survey", name="question.response", methods="POST")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id": "uuid"}})
     */
    public function result(Request $request, GeoObject $geoObject, Question $question): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface || !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }

        $answers = $request->get('answers');
        $files = $request->files->get('answers');

        $r = [];

        foreach ($answers as $a) {
            if (isset($a['id'])) {
                $r[$a['id']] = [];
            }
        }

        foreach ($answers as $a) {
            foreach ($a as $key => $item) {
                if (isset($r[$key])) {
                    $r[$key] += $item;
                }
            }
        }
        if ($files) {
            foreach ($files as $file) {
                foreach ($file as $key => $item) {
                    if (isset($r[$key])) {
                        $r[$key] += $item;
                    }
                }
            }
        }

        $question->response($r, $geoObject, $user);

        $event = new GeoObjectSurveyTouch($geoObject, $user);
        $this->eventDispatcher->dispatch($event, GeoObjectSurveyTouch::NAME);

        return new JsonResponse([]);
    }

    /**
     * @Route("/{id}/confirm", name="confirm", methods="POST")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id": "uuid"}})
     */
    public function confirm(GeoObject $geoObject): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface || !$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }

        /** @var Connection $conn */
        $conn = $this->getDoctrine()->getConnection();

        $stmt = $conn->prepare('
            UPDATE
                x_survey.response_location rl
            SET
                is_confirmed = true,
                confirmed_at = NOW()
            WHERE
                rl.user_id = :user_id
                AND rl.geo_object_id = (
                    SELECT
                        id
                    FROM
                        x_geospatial.geo_object g
                    WHERE
                        g.uuid = :geo_object_uuid
                )
        ');

        $stmt->bindValue('user_id', $user->getId());
        $stmt->bindValue('geo_object_uuid', $geoObject->getUuid());
        $stmt->execute();

        return new JsonResponse([
            'confirmed' => true,
        ]);
    }
}
