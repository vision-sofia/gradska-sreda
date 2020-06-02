<?php

namespace App\Services;

class JsonUtils
{
    public function concatString(?array $data, string $key, string $value): string
    {
        if ($data === null) {
            return '{' . '"' . $key . '":' . $value . '}';
        }

        return substr(json_encode($data), 0, -1) . ',' . '"' . $key . '":' . $value . '}';
    }

    public function joinArray(array $objects): string
    {
        return '[' . implode(',', $objects) . ']';
    }
}
