<?php

namespace App\Services\Geometry;

use Doctrine\ORM\EntityManagerInterface;

class Simplify
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getTolerance(float $zoom): float
    {
    }
}
