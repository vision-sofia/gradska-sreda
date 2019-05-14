<?php

namespace App\Services\Survey\Denormalize;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;

class MatView
{
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function refresh(): void
    {
        $conn = $this->em->getConnection();

        try {
            $conn->query('REFRESH MATERIALIZED VIEW x_survey.ev_criterion_question');
            $conn->query('REFRESH MATERIALIZED VIEW x_survey.geo_object_question');
        } catch (DBALException $e) {
            throw $e;
        }
    }
}
