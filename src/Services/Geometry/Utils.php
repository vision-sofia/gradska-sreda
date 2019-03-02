<?php

namespace App\Services\Geometry;

class Utils
{
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

    public function findTolerance(array $array, float $zoom): ?float
    {
        foreach ($array as $item) {
            if ($zoom <= (float) $item['min_zoom']
                && $zoom >= $item['max_zoom']) {
                return $item['tolerance'];
            }
        }

        return null;
    }
}
