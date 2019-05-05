<?php

namespace App\AppMain\Repository\Survey\Question;

use App\AppMain\DTO\QuestionDTO;
use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey\Question\Question;
use App\AppMain\Entity\User\UserInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;


class QuestionRepository extends EntityRepository
{
    public function findNextQuestion(UserInterface $user, GeoObject $geoObject): ?Question
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Question::class, 'q');
        $rsm->addFieldResult('q', 'id', 'id');
        $rsm->addFieldResult('q', 'title', 'title');
        $rsm->addFieldResult('q', 'uuid', 'uuid');
        $rsm->addFieldResult('q', 'has_multiple_answers', 'hasMultipleAnswers');

        $query = $this->_em->createNativeQuery('
            SELECT
                q.id,
                q.title,
                q.uuid,
                q.has_multiple_answers
            FROM
                x_survey.survey_element l
                    INNER JOIN
                x_survey.survey_category c ON l.category_id = c.id
                    INNER JOIN
                x_survey.q_question q ON q.category_id = c.id
                    INNER JOIN
                x_survey.survey s ON c.survey_id = s.id
            WHERE
                l.object_type_id = :object_type_id
                AND s.is_active = TRUE
                AND NOT EXISTS(
                    SELECT
                        *
                    FROM
                        x_survey.response_question rq
                    WHERE
                        user_id = :user_id 
                        AND rq.question_id = q.id
                        AND rq.geo_object_id = :geo_object_id
                ) 
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
                        AND f.question_id = q.id
                )
            ORDER BY 
                survey_id ASC, 
                q.id ASC                               
            LIMIT 1
        ', $rsm);

        $query->setParameter('user_id', $user->getId());
        $query->setParameter('geo_object_id', $geoObject->getId());
        $query->setParameter('object_type_id', $geoObject->getType()->getId());

        return $query->getOneOrNullResult();
    }

    /**
     * @return \Generator|QuestionDTO
     */
    public function findQuestions(UserInterface $user, GeoObject $geoObject): \Generator
    {

        $conn = $this->_em->getConnection();

        $stmt = $conn->prepare('
                SELECT
                    gq.question_id as id,
                    gq.question_uuid as uuid,
                    gq.question_title as title,
                    gq.question_has_multiple_answers as has_multiple_answers,
                    gq.question_answers as answers,
                    COALESCE(rq.is_completed, FALSE) as is_completed
                FROM
                    x_survey.geo_object_question gq   
                        LEFT JOIN 
                    x_survey.response_question rq ON rq.question_id = gq.question_id
                WHERE
                    gq.geo_object_type_id = :object_type_id
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
                            AND f.question_id = gq.question_id
                    )
                ORDER BY 
                    survey_id ASC, 
                    gq.question_id ASC');

        $stmt->bindValue('user_id', $user->getId());
        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->bindValue('object_type_id', $geoObject->getType()->getId());
        $stmt->execute();

        $stmt->setFetchMode(\PDO::FETCH_CLASS, QuestionDTO::class);

        while ($row = $stmt->fetch()) {
            yield $row;
        }
    }
}
