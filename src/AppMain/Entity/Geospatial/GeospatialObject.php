<?php


namespace App\AppMain\Entity\Geospatial;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="geospatial_object", schema="x_geospatial")
 * @ORM\Entity()
 */
class GeospatialObject implements UuidInterface
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
}