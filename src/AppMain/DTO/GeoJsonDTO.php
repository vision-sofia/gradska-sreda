<?php

/**
 * Properties naming convention: underscore.
 */

namespace App\AppMain\DTO;

class GeoJsonDTO
{
    public string $type = 'Feature';
    public object $geometry;
    public object $properties;

    public function __construct(object $geometry, object $properties)
    {
        $this->geometry = $geometry;
        $this->properties = $properties;
    }
}
