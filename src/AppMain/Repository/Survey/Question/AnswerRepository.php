<?php

namespace App\AppMain\Repository\Survey\Question;

use App\AppMain\Entity\Survey\Question\Answer;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class AnswerRepository extends EntityRepository
{
    /** @deprecated  */
    public function findByQuestion(int $id)
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Answer::class, 'a');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addFieldResult('a', 'title', 'title');
        $rsm->addFieldResult('a', 'uuid', 'uuid');
        $rsm->addJoinedEntityResult(Answer::class, 'p', 'a', 'parent');
        $rsm->addFieldResult('p', 'parent', 'id');

        $query = $this->_em->createNativeQuery('
            WITH RECURSIVE tree as (
                SELECT 
                    id, 
                    uuid, 
                    question_id, 
                    parent,
                    title, 
                    id AS parent_order, 
                    null::int as child_order
                FROM 
                    x_survey.q_answer
                WHERE 
                    parent IS NULL 
                    AND question_id = :question_id      
            
               UNION ALL
               
               SELECT 
                    c.id, 
                    c.uuid, 
                    c.question_id,
                    c.parent,
                    c.title, 
                    p.parent_order, 
                    c.id AS child_order
               FROM 
                    x_survey.q_answer c
                        INNER JOIN 
                    tree p ON p.id = c.parent
               WHERE 
                    c.question_id = :question_id                 
            )
            SELECT 
                * 
            FROM 
                tree
            ORDER BY
                parent_order, child_order NULLS FIRST
        ', $rsm);

        $query->setParameter('question_id', $id);

        return $query->getResult();
    }
}
