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
            $i++;

            $pieces[] = $item;

            if ($i % 2 === 0) {
                $pieces[] = ',';
            }
        }

        array_pop($pieces);

        return implode(' ', $pieces);
    }
}