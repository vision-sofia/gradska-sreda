<?php


namespace App\AppMain\Entity\Geospatial;

use App\AppMain\Entity\Traits\UUIDableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="geo_object_metadata", schema="x_geospatial")
 * @ORM\Entity(repositoryClass="App\AppMain\Repository\Geospatial\GeoObjectRepository")
 */
class GeoObjectMetadata
{
    use UUIDableTrait;

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="App\AppMain\Entity\Geospatial\GeoObject")
     */
    private $geoObject;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasActiveSurvey;
}