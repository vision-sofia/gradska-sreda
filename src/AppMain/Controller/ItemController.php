<?php

namespace App\AppMain\Controller;

use App\AppMain\DTO\QuestionDTO;
use App\AppMain\DTO\UserAnswerDTO;
use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey;
use App\Event\GeoObjectSurveyTouch;
use App\Services\Survey\Response\QuestionV2;
use App\Services\UploaderHelper;
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
    protected $uploaderHelper;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        UploaderHelper $uploaderHelper
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->uploaderHelper = $uploaderHelper;
    }

/*    private function objectToObject($instance, $className) {
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen($className),
            $className,
            strstr(strstr(serialize($instance), '"'), ':')
        ));
    }*/

    /**
     * @Route("geo/{id}", name="app.geo-object.details")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id" = "uuid"}})
     */
    public function details(GeoObject $geoObject): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app.login');
        }

        /** @var Connection $conn */
        $conn = $this->getDoctrine()->getConnection();

        $isAvailableForSurvey = $this->getDoctrine()
            ->getRepository(GeoObject::class)
            ->isAvailableForSurvey($geoObject);
        $questions = [];
        if ($isAvailableForSurvey) {
            $questions = $this->getDoctrine()
                ->getRepository(Survey\Question\Question::class)
                ->findQuestions($this->getUser(), $geoObject);


            $stmt = $conn->prepare('
            SELECT
                *
            FROM
                x_survey.geo_object_question gq
                   
            WHERE
                gq.object_type_id = :object_type_id
                AND gq.survey_is_active = TRUE
                AND NOT EXISTS(
                    SELECT
                        *
                    FROM
                        x_survey.q_flow f
                            INNER JOIN
                        x_survey.response_answer a ON f.answer_id = a.answer_id
                            INNER JOIN
                        x_survey.response_question rq ON a.question_id = rq.id
                    WHERE
                        rq.user_id = :user_id 
                        AND rq.geo_object_id = :geo_object_id
                        AND f.question_id = gq.id
                )
            ORDER BY 
                survey_id ASC, 
                gq.id ASC');

            $stmt->bindValue('user_id', $this->getUser()->getId());
            $stmt->bindValue('geo_object_id', $geoObject->getId());
            $stmt->bindValue('object_type_id', $geoObject->getType()->getId());
            $stmt->execute();

            $result = [];
            $stmt->setFetchMode(\PDO::FETCH_CLASS, QuestionDTO::class);

            /** @var QuestionDTO $row */
            while ($row = $stmt->fetch()) {

                foreach (json_decode($row->getAnswers()) as $item) {
                  #  dump($this->objectToObject($item, UserAnswerDTO::class));
                }


              #  $row->setAnswers();
                $result[] = $row;
            }

            dump($result);
        }


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
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

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
            ORDER BY u.username                ');

        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->execute();

        $resultByUsers = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $resultByUsers[] = $row;
        }

        $stmt = $conn->prepare('
            SELECT
                ra.answer_id,
                ra.explanation,
                ra.photo,
                a.is_free_answer
            FROM
                x_survey.response_answer ra
                    INNER JOIN
                x_survey.response_question rq ON ra.question_id = rq.id
                    INNER JOIN
                x_survey.q_answer a ON ra.answer_id = a.id
            WHERE
                rq.geo_object_id = :geo_object_id
                AND rq.user_id = :user_id
        ');

        $stmt->bindValue('user_id', $this->getUser()->getId());
        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->execute();

        $response = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if (!$row['is_free_answer']) {
                unset($row['explanation']);
            }

            $response[$row['answer_id']] = $row;
        }

        return $this->render('front/geo-object/details.html.twig', [
            'geo_object' => $geoObject,
            'questions' => $questions,
            'is_available_for_survey' => $isAvailableForSurvey,
            'result' => $result,
            'resultByUsers' => $resultByUsers,
            'response' => $response,
        ]);
    }

    /**
     * @Route("geo/{id}/result", name="app.geo-object.result", methods="POST")
     * @ParamConverter("geoObject", class="App\AppMain\Entity\Geospatial\GeoObject", options={"mapping": {"id" = "uuid"}})
     */
    public function result(Request $request, GeoObject $geoObject, QuestionV2 $question): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app.login');
        }

        /** @var \Doctrine\DBAL\Connection $conn */
        $conn = $this->getDoctrine()->getConnection();
        $stmt = $conn->prepare('
                SELECT
                    a.uuid,
                    rq.question_id
                FROM
                    x_survey.response_answer ra
                        INNER JOIN
                    x_survey.response_question rq ON ra.question_id = rq.id
                        INNER JOIN
                    x_survey.q_answer a ON a.id = ra.answer_id                        
                      
                WHERE
                    rq.geo_object_id = :geo_object_id
                    AND rq.is_latest = TRUE
                    AND rq.user_id = :user_id
            ');

        $stmt->bindValue('user_id', $this->getUser()->getId());
        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->execute();

        $currentAnswers = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $currentAnswers[] = $row['uuid'];
        }

        $answers = $request->get('answers');
        $files = $request->files->get('answers');

        $r = [];

        // Options
        if (isset($answers['option']) && \is_array($answers['option'])) {
            foreach ($answers['option'] as $value) {
                foreach ($value as $k => $a) {
                    if ('id' === $k) {
                        if (\is_array($a)) {
                            foreach ($a as $item) {
                                $r[$item] = [];
                            }
                        } else {
                            $r[$a] = [];
                        }
                    }
                }
            }
        }

        // Explanation
        if (isset($answers['explanation']) && \is_array($answers['explanation'])) {
            foreach ($answers['explanation'] as $key => $item) {
                if (isset($r[$key])) {
                    $r[$key]['explanation'] = $item;
                }
            }
        }

        // Photo
        if (isset($files['photo']) && \is_array($files['photo'])) {
            foreach ($files['photo'] as $key => $item) {
                if (isset($r[$key])) {
                    $r[$key]['photo'] = $item;
                }
            }
        }

        $d = [];

        foreach ($r as $key => $item) {
            $d[] = $key;
        }

        // new
        $diff = array_diff($d, $currentAnswers);

        foreach ($diff as $item) {
            $question->response($item, $r[$item], $geoObject, $this->getUser());
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

        $diff = array_diff($currentAnswers, $d);

        foreach ($diff as $item) {
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

        foreach ($r as $key => $value) {
            if (isset($value['photo'])) {
                $photo = $value['photo'];

                $file = $this->uploaderHelper->uploadDataFile($photo);

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


                $stmt->bindValue('photo', $file->getFilename());
                $stmt->bindValue('answer_uuid', $key);
                $stmt->bindValue('user_id', $this->getUser()->getId());
                $stmt->bindValue('geo_object_id', $geoObject->getId());
                $stmt->execute();
            }
        }


        // Remove
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
        /*
                return $this->redirectToRoute('app.geo-object.details', [
                    'id' => $geoObject->getUuid(),
                ]);
        */
    }
}
