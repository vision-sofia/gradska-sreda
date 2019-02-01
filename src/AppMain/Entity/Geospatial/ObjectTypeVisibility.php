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
     * @ORM\Column(type="smallint")
     */
    private $zoomThreshold;

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

    public function getZoomThreshold(): ?int
    {
        return $this->zoomThreshold;
    }

    public function setZoomThreshold(int $zoomThreshold): void
    {
        $this->zoomThreshold = $zoomThreshold;
    }
}
