<?php


namespace App\Services;


class JsonUtils
{
    public function concatString(array $data, string $key, string $value): string
    {
        return substr(json_encode($data), 0, -1) . ',' . '"' . $key . '":' . $value . '}';
    }

    public function joinArray(array $objects): string
    {
        return '[' . implode(',', $objects) . ']';
    }
}