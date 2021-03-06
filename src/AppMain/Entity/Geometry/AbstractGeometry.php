<?php

namespace App\AppMain\Entity\Geometry;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

/** @MappedSuperclass */
abstract class AbstractGeometry implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="geometry", nullable=true, options={"geometry_type": "geometry", "srid": 4326})
     */
    protected $coordinates;

    /**
     * @ORM\Column(type="json", options={"jsonb": true}, nullable=true)
     */
    protected $metadata;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\GeoObject", inversedBy="geography")
     */
    protected $geoObject;
}
