<?php


namespace App\AppMain\Entity\Geospatial;

use App\AppMain\Entity\Geometry\GeometryBase;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="geo_object", schema="x_geospatial")
 * @ORM\Entity()
 */
class GeoObject implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(type="json_array", options={"jsonb": true}, nullable=true)
     */
    private $attributes;

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

    public function getId():?int
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

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes($attributes): void
    {
        $this->attributes = $attributes;
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
}