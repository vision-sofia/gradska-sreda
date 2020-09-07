<?php

/**
 * Properties naming convention: underscore.
 */

namespace App\AppMain\DTO;

class SurveyGeoObjectDTO
{
    public ?int $id = null;
    public $geometry;
    public $geometry_type;
    public $base_style;
    public $hover_style;
    public $active_style;
    public $type_name;
    public $geo_name;
    public string $uuid;
    public $properties;
    public $bounding_box;
}
