<?php
/**
 * Properties naming convention: underscore
 */

namespace App\AppMain\DTO;

class SurveyGeoObjectDTO
{
    public $id;
    public $geometry;
    public $geometry_type;
    public $base_style;
    public $hover_style;
    public $active_style;
    public $type_name;
    public $geo_name;
    public $uuid;
    public $properties;
    public $bounding_box;
}
