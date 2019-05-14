<?php

namespace App\AppMain\Entity\Geospatial;

use App\AppMain\Entity\Geometry\GeometryBase;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="geo_object", schema="x_geospatial")
 * @ORM\Entity(repositoryClass="App\AppMain\Repository\Geospatial\GeoObjectRepository")
 */
class GeoObject implements UuidInterface, GeoObjectInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(type="json_array", options={"jsonb": true, "default" = "{}"})
     */
    private $properties;

    /**
     * @ORM\Column(type="json_array", options={"jsonb": true, "default" = "{}"})
     */
    private $localProperties;

    /**
     * @ORM\OneToMany(targetEntity="App\AppMain\Entity\Geometry\GeometryBase", mappedBy="geoObject")
     */
    private $geography;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\ObjectType")
     * @ORM\JoinColumn(referencedColumnName="id", name="object_type_id", nullable=true)
     */
    private $objectType;

    /**
     * @ORM\Column(type="string")
     */
    private $name = '';

    /**
     * @ORM\Column(type="json_array", options={"jsonb": true}, nullable=true)
     */
    private $metadata;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getGeography(): ?GeometryBase
    {
        return $this->geography;
    }

    public function setGeography(GeometryBase $geography): void
    {
        $this->geography = $geography;
    }

    public function getType(): ?ObjectType
    {
        return $this->objectType;
    }

    public function setType(?ObjectType $objectType): void
    {
        $this->objectType = $objectType;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function getObjectType()
    {
        return $this->objectType;
    }

    public function setObjectType($objectType): void
    {
        $this->objectType = $objectType;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getLocalProperties(): ?array
    {
        return $this->localProperties;
    }

    public function setLocalProperties($localProperties): void
    {
        $this->localProperties = $localProperties;
    }


}
