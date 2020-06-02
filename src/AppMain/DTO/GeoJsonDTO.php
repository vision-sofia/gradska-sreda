<?php

/**
 * Properties naming convention: underscore.
 */

namespace App\AppMain\DTO;

class GeoJsonDTO
{
    public $type = 'Feature';
    public $geometry;
    public $properties;

    /**
     * GeoJsonDTO constructor.
     *
     * @param $geometry
     * @param $properties
     */
    public function __construct(object $geometry, object $properties)
    {
        $this->geometry = $geometry;
        $this->properties = $properties;
    }
}
