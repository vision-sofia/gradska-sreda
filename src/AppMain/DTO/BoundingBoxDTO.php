<?php
/**
 * Properties naming convention: underscore
 */

namespace App\AppMain\DTO;

class BoundingBoxDTO
{
    private $x_min;
    private $x_max;
    private $y_min;
    private $y_max;

    public function getXMin(): float
    {
        return $this->x_min;
    }

    public function getXMax(): float
    {
        return $this->x_max;
    }

    public function getYMin(): float
    {
        return $this->y_min;
    }

    public function getYMax(): float
    {
        return $this->y_max;
    }
}
