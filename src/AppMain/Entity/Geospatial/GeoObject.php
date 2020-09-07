<?php

namespace App\AppMain\Entity\Geospatial;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="geo_object", schema="x_geospatial")
 * @ORM\Entity
 */
class GeoObject implements UuidInterface, GeoObjectInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @ORM\Column(type="json", options={"jsonb": true, "default": "{}"})
     */
    private ?array $properties = null;

    /**
     * @ORM\Column(type="json", options={"jsonb": true, "default": "{}"})
     */
    private ?array $localProperties = null;

    /**
     * @ORM\OneToMany(targetEntity="App\AppMain\Entity\Geometry\GeometryBase", mappedBy="geoObject")
     */
    private ?Collection $geography = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\ObjectType")
     * @ORM\JoinColumn(referencedColumnName="id", name="object_type_id", nullable=true)
     */
    private $objectType;

    /**
     * @ORM\Column(type="string")
     */
    private string $name = '';

    /**
     * @ORM\Column(type="json", options={"jsonb": true}, nullable=true)
     */
    private ?array $metadata = null;

    public function getId(): int
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
