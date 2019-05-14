<?php

namespace App\Services\Geospatial;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;

class Simplify
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
            $conn->query('REFRESH MATERIALIZED VIEW x_geometry.simplified_geo');
        } catch (DBALException $e) {
            throw $e;
        }
    }
}
