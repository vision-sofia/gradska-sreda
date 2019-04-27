<?php

namespace App\Services\Geospatial;

use App\AppMain\Entity\Geospatial\StyleCondition;

class Style
{
    public function formatStyle(array $newStyles, array $oldStyles): array
    {
        $result = [];

        foreach ($newStyles as $k => $v) {
            if (empty($v)) {
                continue;
            }

            $lines = explode("\n", $v);

            if (!isset($oldStyles[$k])) {
                $result[$k]['code'] = bin2hex(random_bytes(3));
            } else {
                $result[$k]['code'] = $oldStyles[$k]['code'];
            }

            /*
            foreach ($lines as $line) {
                $lineParts = explode(':', trim($line));

                if (isset($lineParts[0], $lineParts[1])) {
                    $lineParts[0] = trim($lineParts[0]);
                    $lineParts[1] = trim($lineParts[1]);

                    $result[$k]['content'][$lineParts[0]] = $lineParts[1];
                }
            }
            */

            foreach ($lines as $line) {
                $lineParts = explode(':', trim($line));

                if (isset($lineParts[0], $lineParts[1])) {
                    $lineParts[0] = trim($lineParts[0]);
                    $lineParts[1] = rtrim(trim($lineParts[1]), ',');
                    $lineParts[1] = str_replace('"', '', $lineParts[1]);

                    if (in_array($lineParts[0], [
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
                    ])) {
                        $result[$k]['content'][$lineParts[0]] = ('true' === $lineParts[1]);
                    } elseif (in_array($lineParts[0], [
                        'width',
                        'borderWidth',
                        'shadowWidth',
                        'interactiveWidth',
                        'opacity',
                        'weight',
                        'dashOffset',
                    ])) {
                        $result[$k]['content'][$lineParts[0]] = (int) $lineParts[1];
                    } else {
                        $result[$k]['content'][$lineParts[0]] = $lineParts[1];
                    }
                }
            }
        }

        return $result;
    }

    public function toText(StyleCondition $styleCondition): array
    {
        $styles = [
            'base'  => [
                'point'   => '',
                'line'    => '',
                'polygon' => '',
            ],
            'hover' => [
                'point'   => '',
                'line'    => '',
                'polygon' => '',
            ],
        ];

        foreach ($styleCondition->getBaseStyle() as $key => $value) {
            if (!isset($styles['base'][$key])) {
                $styles['base'][$key] = '';
            }

            if (isset($value['content'])) {
                foreach ($value['content'] as $k => $v) {
                    $k = trim($k);
                    $v = trim($v);

                    $styles['base'][$key] .= sprintf("%s:%s\n", $k, $v);
                }
            }
        }

        foreach ($styleCondition->getHoverStyle() as $key => $value) {
            if (!isset($styles['hover'][$key])) {
                $styles['hover'][$key] = '';
            }

            if (isset($value['content'])) {
                foreach ($value['content'] as $k => $v) {
                    $k = trim($k);
                    $v = trim($v);

                    $styles['hover'][$key] .= sprintf("%s: %s\n", $k, $v);
                }
            }
        }

        return $styles;
    }
}
