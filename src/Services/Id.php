<?php


namespace App\Services;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

class Id extends AbstractIdGenerator
{

    /**
     * Generates an identifier for an entity.
     *
     * @param EntityManager $em
     * @param object|null $entity
     *
     * @return mixed
     */
    public function generate(EntityManager $em, $entity)
    {
        return null;
    }

    public function isPostInsertGenerator()
    {
        return true;
    }
}