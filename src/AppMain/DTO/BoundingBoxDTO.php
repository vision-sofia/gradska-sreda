<?php

/**
 * Properties naming convention: underscore.
 */

namespace App\AppMain\DTO;

class BoundingBoxDTO
{
    private ?float $x_min = null;
    private ?float $x_max = null;
    private ?float $y_min = null;
    private ?float $y_max = null;
    private ?string $envelope = null;
    private ?string $properties = null;

    public function getXMin(): ?float
    {
        return $this->x_min;
    }

    public function getXMax(): ?float
    {
        return $this->x_max;
    }

    public function getYMin(): ?float
    {
        return $this->y_min;
    }

    public function getYMax(): ?float
    {
        return $this->y_max;
    }

    public function getPolygon(): ?string
    {
        return $this->envelope;
    }

    public function getProperties(): ?string
    {
        return $this->properties;
    }
}
