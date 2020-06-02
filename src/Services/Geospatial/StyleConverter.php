<?php

namespace App\Services\Geospatial;

use App\AppMain\Entity\Geospatial\StyleCondition;

class StyleConverter
{
    public function textToGroupStyle(string $v): array
    {
        $result = [];

        $lines = explode("\n", $v);

        foreach ($lines as $line) {
            $style = $this->cast($line);

            if ($style) {
                $result[$style['option']] = $style['value'];
            }
        }

        return $result;
    }

    public function formatConditionStyle(array $newStyles, array $oldStyles): array
    {
        $result = [];

        foreach ($newStyles as $k => $v) {
            if (empty($v)) {
                continue;
            }

            if (!isset($oldStyles[$k])) {
                $result[$k]['code'] = bin2hex(random_bytes(3));
            } else {
                $result[$k]['code'] = $oldStyles[$k]['code'];
            }

            $lines = explode("\n", $v);

            foreach ($lines as $line) {
                $s = $this->cast($line);

                if ($s) {
                    $result[$k]['content'][$s['option']] = $s['value'];
                }
            }
        }

        return $result;
    }

    private function cast(string $line): array
    {
        $types = [
            'bool' => [
                'border',
                'transparent',
                'hover',
                'onlyShowOnHover',
                'shadow',
                'shadowWhenInteractive',
                'shadowWhenPopupOpen',
                'addInteractiveLayerGroup',
                'addInteractive',
                'interactive',
                'fill',
                'dashOffset',
            ],
            'int' => [
                'width',
                'borderWidth',
                'shadowWidth',
                'interactiveWidth',
                'weight',
            ],
            'float' => [
                'opacity',
                'fillOpacity',
            ],
        ];

        $lineParts = explode(':', trim($line));

        if (isset($lineParts[0], $lineParts[1])) {
            $lineParts[0] = trim($lineParts[0]);
            $lineParts[1] = rtrim(trim($lineParts[1]), ',');
            $lineParts[1] = str_replace('"', '', $lineParts[1]);

            if (\in_array($lineParts[0], $types['bool'], true)) {
                return [
                    'option' => $lineParts[0],
                    'value' => 'true' === $lineParts[1],
                ];
            }

            if (\in_array($lineParts[0], $types['int'], true)) {
                return [
                    'option' => $lineParts[0],
                    'value' => (int) $lineParts[1],
                ];
            }

            if (\in_array($lineParts[0], $types['float'], true)) {
                return [
                    'option' => $lineParts[0],
                    'value' => (float) $lineParts[1],
                ];
            }

            return [
                'option' => $lineParts[0],
                'value' => $lineParts[1],
            ];
        }

        return [];
    }

    private function buildLine(string $key, $value): string
    {
        $key = trim($key);

        if (\is_bool($value)) {
            return sprintf("%s: %s,\n", $key, ($value === true ? 'true' : 'false'));
        }

        if (\is_string($value)) {
            return sprintf("%s: %s,\n", $key, rtrim('"' . trim($value) . '"', ','));
        }

        return sprintf("%s: %s,\n", $key, rtrim(trim($value), ','));
    }

    public function styleToText(array $array): string
    {
        $style = '';

        foreach ($array as $k => $v) {
            $style .= $this->buildLine($k, $v);
        }

        return $style;
    }

    public function conditionStyleToText(StyleCondition $styleCondition): array
    {
        $styles = [
            'base' => [
                'POINT' => '',
                'LINESTRING' => '',
                'POLYGON' => '',
            ],
            'hover' => [
                'POINT' => '',
                'LINESTRING' => '',
                'POLYGON' => '',
            ],
        ];

        foreach ($styleCondition->getBaseStyle() as $key => $value) {
            if (!isset($styles['base'][$key])) {
                $styles['base'][$key] = '';
            }

            if (isset($value['content'])) {
                $styles['base'][$key] = $this->styleToText($value['content']);
            }
        }

        foreach ($styleCondition->getHoverStyle() as $key => $value) {
            if (!isset($styles['hover'][$key])) {
                $styles['hover'][$key] = '';
            }

            if (isset($value['content'])) {
                $styles['hover'][$key] = $this->styleToText($value['content']);
            }
        }

        return $styles;
    }
}
