<?php

namespace App\AppMain\Entity\Geospatial;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="object_type_visibility", schema="x_geospatial")
 * @ORM\Entity()
 */
class ObjectTypeVisibility implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\ObjectType")
     * @ORM\JoinColumn(referencedColumnName="id", name="object_type_id", nullable=false)
     */
    private $objectType;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2)
     */
    private $minZoom;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2)
     */
    private $maxZoom;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjectType(): ?ObjectType
    {
        return $this->objectType;
    }

    public function setObjectType(ObjectType $objectType): void
    {
        $this->objectType = $objectType;
    }

    public function getMinZoom(): ?float
    {
        return $this->minZoom;
    }

    public function setMinZoom(float $minZoom): void
    {
        $this->minZoom = $minZoom;
    }

    public function getMaxZoom(): ?float
    {
        return $this->maxZoom;
    }

    public function setMaxZoom(float $maxZoom): void
    {
        $this->maxZoom = $maxZoom;
    }
}
