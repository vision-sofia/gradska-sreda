<?php


namespace App\AppMain\Entity\Geospatial;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="geospatial_object", schema="x_geospatial")
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
     * @ORM\OneToMany(targetEntity="App\AppMain\Entity\Geometry\GeometryBase", mappedBy="spatialObject")
     */
    private $geography;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\Layer")
     */
    private $layer;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes($attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getGeography()
    {
        return $this->geography;
    }

    public function setGeography($geography): void
    {
        $this->geography = $geography;
    }

    public function getLayer()
    {
        return $this->layer;
    }

    public function setLayer($layer): void
    {
        $this->layer = $layer;
    }
}