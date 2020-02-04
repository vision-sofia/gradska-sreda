<?php

namespace App\Services\Survey\Denormalize;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;

class MatView
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function refresh(): void
    {
        $conn = $this->entityManager->getConnection();

        try {
            $conn->query('REFRESH MATERIALIZED VIEW x_survey.ev_criterion_question');
            $conn->query('REFRESH MATERIALIZED VIEW x_survey.geo_object_question');
        } catch (DBALException $e) {
            throw $e;
        }
    }
}
