<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey\Question\Answer;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\AppMain\Entity\Survey;



class ItemController extends AbstractController
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("geo/{id}", name="app.geospatial_object.details")
     * @ParamConverter("geospatialObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id" = "uuid"}})
     */
    public function details(GeoObject $geospatialObject): Response
    {
        $type = $geospatialObject->getAttributes()['type'];

        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('
            SELECT
                q.*
            FROM
                x_survey.object_layer l
                    INNER JOIN
                x_geospatial.layer gl ON gl.id = l.layer_id
                    INNER JOIN
                x_survey.category c ON l.category_id = c.id
                    INNER JOIN
                x_survey.q_question q ON q.category_id = c.id
            WHERE
                gl.name = :name
            LIMIT 1        
        ');

        $stmt->bindValue('name', 'тротоар');
        $stmt->execute();

        $question = $stmt->fetch();

        $answers = $this->getDoctrine()
                        ->getRepository(Answer::class)
                        ->findByQuestion($question['id']);


        return $this->render('front/geo-object/details.html.twig', [
            'geo_object' => $geospatialObject,
            'answers' => $answers
        ]);
    }

    /**
     * @Route("geo/{id}/result", name="app.geospatial_object.result")
     * @ParamConverter("geospatialObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id" = "uuid"}})
     */
    public function result(Request $request, GeoObject $geospatialObject): Response
    {
        $answerUuid = $request->get('parent');
        $answer = $this->getDoctrine()->getRepository(Answer::class)->findOneBy([
            'uuid' =>  $answerUuid
        ]);


        // BEFORE INSERT trigger simulation
        $conn = $this->entityManager->getConnection();
        $stmt = $conn->prepare('
            UPDATE
                x_survey.response_question
            SET
                is_latest = false
            WHERE
                user_id = :user_id
                AND question_id = :question_id
                AND geo_object_id = :geo_object_id
        ');

        $stmt->bindValue('user_id', $this->getUser()->getId());
        $stmt->bindValue('question_id', $answer->getQuestion()->getId());
        $stmt->bindValue('geo_object_id', $geospatialObject->getId());
        $stmt->execute();

        $responseQuestion = new Survey\Response\Question();
        $responseQuestion->setUser($this->getUser());
        $responseQuestion->setGeoObject($geospatialObject);
        $responseQuestion->setQuestion($answer->getQuestion());
        $responseQuestion->setIsLatest(true);

        $responseAnswer = new Survey\Response\Answer();
        $responseAnswer->setAnswer($answer);

        $responseQuestion->addAnswer($responseAnswer);

        $this->entityManager->persist($responseQuestion);
        $this->entityManager->flush();

        $type = $geospatialObject->getAttributes()['type'];

        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('
            SELECT
                q.*
            FROM
                x_survey.object_layer l
                    INNER JOIN
                x_geospatial.layer gl ON gl.id = l.layer_id
                    INNER JOIN
                x_survey.category c ON l.category_id = c.id
                    INNER JOIN
                x_survey.q_question q ON q.category_id = c.id
            WHERE
                gl.name = :name
            LIMIT 1        
        ');

        $stmt->bindValue('name', 'тротоар');
        $stmt->execute();

        $question = $stmt->fetch();

        $answers = $this->getDoctrine()
                        ->getRepository(Answer::class)
                        ->findByQuestion($question['id']);


        return $this->render('front/geo-object/details.html.twig', [
            'geo_object' => $geospatialObject,
            'answers' => $answers
        ]);
    }
}
