<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey;
use App\Services\Survey\Response\QuestionV2;
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
        $questions = [];
        if ($isAvailableForSurvey) {
            $questions = $this->getDoctrine()
                              ->getRepository(Survey\Question\Question::class)
                              ->findQuestions($this->getUser(), $geoObject)
            ;
        }

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
                ra.answer_id
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

        $response = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $response[$row['answer_id']] = $row['answer_id'];
        }

        return $this->render('front/geo-object/details.html.twig', [
            'geo_object'              => $geoObject,
            'questions'               => $questions,
            'is_available_for_survey' => $isAvailableForSurvey,
            'result'                  => $result,
            'resultByUsers'           => $resultByUsers,
            'response'                => $response,
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

        // Explanation
        foreach ($answers['explanation'] as $key => $item) {
            if(isset($r[$key])) {
                $r[$key]['explanation'] = $item;
            }
        }

        // Photo
        foreach ($files['photo'] as $key => $item) {
            if(isset($r[$key])) {
                $r[$key]['photo'] = $item;
            }
        }
        
        
        $d = [];

        foreach ($r as $key => $item) {
            $d[] = $key;
        }


        // new
        $result = array_diff($d, $currentAnswers);

        foreach ($result as $item) {
              $question->response($item, $r[$item],  $geoObject, $this->getUser());
        }

        //   dump($result);

        // old
        $odlResult = array_diff($currentAnswers, $d);

        $q = $conn->prepare('
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

        $q->bindValue('user_id', $this->getUser()->getId());
        $q->bindValue('geo_object_id', $geoObject->getId());

        foreach ($odlResult as $item) {
            $q->bindValue('answer_uuid', $item);
            $q->execute();
        }


        /*
                foreach ($value['explanation'] as $key => $z) {
                    if (isset($z['explanation'], $r[$key])) {
                        $r[$key]['explanation'] = $z['explanation'];
                    }
                }
        */
/*        foreach ($r as $key => $value) {
            if(isset($files[$key])) {
                $r[$key]['photo'] = $files[$key];
            }
        }*/

       # dump($r);
        //   dump($a);

        $j = [];

        foreach ($answers as $answer) {
            foreach ($answer as $a) {
                if (isset($a['id'])) {
                    $r[$a['id']] = [];
                }
            }

            foreach ($answer as $a) {
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

            $j[] = $r;

            /*  if (!\is_array($answer)) {
                  return $this->redirectToRoute('app.geo-object.details', [
                      'id' => $geoObject->getUuid(),
                  ]);
              }*/

            // $event = new GeoObjectSurveyTouch($geoObject, $this->getUser());
            // $this->eventDispatcher->dispatch(GeoObjectSurveyTouch::NAME, $event);
        }

        $submit = [];
        foreach ($j as $item) {
            $submit[] = key($item);
        }

        $result = array_diff($submit, $currentAnswers);

        //   dump($result, $j);

        foreach ($j as $v) {
            //   dump($j);
            if (\in_array(key($v), $j, true)) {
            }
            // $question->response($v,  $geoObject, $this->getUser());
            //  dump($v);
        }

        // new
        //   $result = array_diff($j, $currentAnswers);

        foreach ($result as $item) {
            //  $question->response($item, $geoObject, $this->getUser());
        }

        //   dump($result);

        // old
        $odlResult = array_diff($currentAnswers, $j);

        //   dump($odlResult);

        /*

        UPDATE
            x_survey.response_answer ra
        SET
            answer_id = (
                SELECT
                    id
                FROM
                    x_survey.q_answer
                WHERE
                    uuid = '9328fe5d-7633-441b-a8a2-9c72c17d4a45'
            )
        FROM
            x_survey.response_question q
                INNER JOIN
            x_survey.q_answer a ON a.id = ra.answer_id
        WHERE
            ra.question_id = q.id
            AND a.uuid = 'ec953811-2356-4248-ac79-130c7ea493f1'

         */

        $q = $conn->prepare('
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

        $q->bindValue('user_id', $this->getUser()->getId());
        $q->bindValue('geo_object_id', $geoObject->getId());

        foreach ($odlResult as $item) {
            $q->bindValue('answer_uuid', $item);
            $q->execute();
        }

        /* return $this->redirectToRoute('app.geo-object.details', [
             'id' => $geoObject->getUuid(),
         ]);*/
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

        if (!\is_array($answers)) {
            return $this->redirectToRoute('app.geo-object.details', [
                'id' => $geoObject->getUuid(),
            ]);
        }

        /*
        $question->response($r, $geoObject, $this->getUser());

        $event = new GeoObjectSurveyTouch($geoObject, $this->getUser());
        $this->eventDispatcher->dispatch(GeoObjectSurveyTouch::NAME, $event);

        return $this->redirectToRoute('app.geo-object.details', [
            'id' => $geoObject->getUuid(),
        ]);
        */
    }
}
