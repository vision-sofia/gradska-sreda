<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey;
use App\Event\GeoObjectSurveyTouch;
use App\Services\Survey\Response\Question;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemController extends AbstractController
{
    protected $entityManager;
    protected $eventDispatcher;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("geo/{id}", name="app.geo-object.details")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id" = "uuid"}})
     */
    public function details(GeoObject $geoObject): Response
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
                ->findNextQuestion($this->getUser(), $geoObject)
            ;
        }

        return $this->render('front/geo-object/details.html.twig', [
            'geo_object' => $geoObject,
            'question' => $question ?? null,
            'is_available_for_survey' => $isAvailableForSurvey,
        ]);
    }

    /**
     * @Route("geo/{id}/result", name="app.geo-object.result")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id" = "uuid"}})
     */
    public function result(Request $request, GeoObject $geoObject, Question $question): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app.login');
        }

        $answers = $request->get('answers');

        if (!\is_array($answers)) {
            return $this->redirectToRoute('app.geo-object.details', [
                'id' => $geoObject->getUuid(),
            ]);
        }

        $question->response($answers, $geoObject, $this->getUser());

        $event = new GeoObjectSurveyTouch($geoObject, $this->getUser());
        $this->eventDispatcher->dispatch(GeoObjectSurveyTouch::NAME, $event);

        return $this->redirectToRoute('app.geo-object.details', [
            'id' => $geoObject->getUuid(),
        ]);
    }
}
