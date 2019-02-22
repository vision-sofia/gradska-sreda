<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey;
use App\Event\GeoObjectSurveyTouch;
use App\Services\Survey\Response\Question;
use Doctrine\DBAL\Driver\Connection;
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

        /** @var Connection $conn */
        $conn = $this->getDoctrine()->getConnection();

        $stmt = $conn->prepare('
            SELECT
                cr.name as name, 
                round(AVG(rating), 1) as rating
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
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        $stmt = $conn->prepare('
            SELECT
                u.username as user_username, 
                cr.name as criterion_name, 
                round(AVG(rating), 2) as rating
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
            ORDER BY u.username                ');

        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->execute();

        $resultByUsers = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $resultByUsers[] = $row;
        }

        return $this->render('front/geo-object/details.html.twig', [
            'geo_object' => $geoObject,
            'question' => $question ?? null,
            'is_available_for_survey' => $isAvailableForSurvey,
            'result' => $result,
            'resultByUsers' => $resultByUsers,
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

        foreach ($files as $file) {
            foreach ($file as $key => $item) {
                if (isset($r[$key])) {
                    $r[$key] += $item;
                }
            }
        }

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
