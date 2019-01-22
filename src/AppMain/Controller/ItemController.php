<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey\Question\Answer;
use App\Event\GeoObjectSurveyTouch;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\AppMain\Entity\Survey;



class ItemController extends AbstractController
{
    protected $entityManager;
    protected $eventDispatcher;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("geo/{id}", name="app.geo-object.details")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id" = "uuid"}})
     */
    public function details(GeoObject $geoObject): Response
    {
        $question = $this->getDoctrine()
                  ->getRepository(Survey\Question\Question::class)
                  ->findNextQuestion(
                      $this->getUser(),
                      $geoObject
                  )
        ;

        return $this->render('front/geo-object/details.html.twig', [
            'geo_object' => $geoObject,
            'question' => $question,
        ]);
    }

    /**
     * @Route("geo/{id}/result", name="app.geo-object.result")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id" = "uuid"}})
     */
    public function result(Request $request, GeoObject $geoObject): Response
    {
        $parent = $request->get('parent');
        $answer = $this->getDoctrine()->getRepository(Answer::class)->findOneBy([
            'uuid' =>  $parent
        ]);

        $child = $request->get('child');

        $childAnswers = [];

        if(!empty($child)) {
            foreach ($child as $item) {
                $answer = $this->getDoctrine()->getRepository(Answer::class)->findOneBy([
                    'uuid' =>  $item
                ]);

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
                            'geoObject' => $geoObject,
                            'user' => $this->getUser(),
                            'coordinates' => null
                         ]);

        if($location === null) {
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
            'id' => $geoObject->getUuid()
        ]);
    }
}
