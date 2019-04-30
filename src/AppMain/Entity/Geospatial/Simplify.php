<?php

namespace App\AppMain\Entity\Geospatial;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use App\Doctrine\ValueObject\IntRange;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="simplify",
 *     schema="x_geospatial",
 *     indexes={
 *         @ORM\Index(columns={"zoom"}, flags={"spatial"})
 *     }
 * )
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
     * @ORM\Column(type="int4range")
     */
    private $zoom;

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

    public function getZoom(): ?IntRange
    {
        return $this->zoom;
    }

    public function setZoom(IntRange $zoom): void
    {
        $this->zoom = $zoom;
    }
}
