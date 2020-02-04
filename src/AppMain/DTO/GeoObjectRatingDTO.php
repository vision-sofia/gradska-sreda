<?php

/**
 * Properties naming convention: underscore
 */

namespace App\AppMain\DTO;

class GeoObjectRatingDTO
{
    public $rating;
    public $max;
    public $criterion;
    public $percentage;

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function getMax(): ?float
    {
        return $this->max;
    }

    public function getCriterion(): string
    {
        return $this->criterion;
    }

    public function getPercentage(): ?float
    {
        return $this->percentage;
    }
}
