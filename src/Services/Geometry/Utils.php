<?php

namespace App\Services\Geometry;

use App\AppMain\DTO\BoundingBoxDTO;

class Utils
{
    public static function buildBboxFromDTO(BoundingBoxDTO $boundingBoxDTO, $asJSON = true)
    {
        return self::buildBbox(
            $boundingBoxDTO->getXMin(),
            $boundingBoxDTO->getYMin(),
            $boundingBoxDTO->getXMax(),
            $boundingBoxDTO->getYMax(),
            $asJSON
        );
    }

    public static function buildBbox(?float $xMin, ?float $yMin, ?float $xMax, ?float $yMax, $asJSON = true)
    {
        if (!$xMin || !$yMin || !$xMin || !$yMin) {
            return null;
        }

        $bbox = [
            [$yMin, $xMin],
            [$yMax, $xMax],
        ];

        return $asJSON ? json_encode($bbox) : $bbox;
    }

    public function parseCoordinates(string $text): string
    {
        $explode = explode(',', $text);

        $pieces = [];

        $i = 0;

        foreach ($explode as $item) {
            ++$i;

            $pieces[] = $item;

            if (0 === $i % 2) {
                $pieces[] = ',';
            }
        }

        array_pop($pieces);

        return implode(' ', $pieces);
    }

    public function bbox(string $text, $element): string
    {
        $explode = explode(',', $text);

        return $explode[$element];
    }

    public function findTolerance(array $array, float $zoom): float
    {
        foreach ($array as $item) {
            if ($zoom <= (float) $item['min_zoom']
                && $zoom >= $item['max_zoom']) {
                return $item['tolerance'];
            }
        }

        return 0.001;
    }
}
