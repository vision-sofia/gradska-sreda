<?php

namespace App\AppMain\Entity\Geospatial;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="simplify", schema="x_geospatial")
 * @ORM\Entity()
 */
class Simplify implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8)
     */
    private $tolerance;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private $minZoom;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private $maxZoom;

    public function getId()
    {
        return $this->id;
    }

    public function getTolerance(): float
    {
        return $this->tolerance;
    }

    public function setTolerance(float $tolerance): void
    {
        $this->tolerance = $tolerance;
    }

    public function getMinZoom(): float
    {
        return $this->minZoom;
    }

    public function setMinZoom(float $minZoom): void
    {
        $this->minZoom = $minZoom;
    }

    public function getMaxZoom(): float
    {
        return $this->maxZoom;
    }

    public function setMaxZoom(float $maxZoom): void
    {
        $this->maxZoom = $maxZoom;
    }
}
