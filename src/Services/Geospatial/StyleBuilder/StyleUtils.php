<?php


namespace App\Services\Geospatial\StyleBuilder;

use App\AppMain\Entity\Geospatial\StyleCondition;
use Doctrine\ORM\EntityManagerInterface;

class StyleUtils
{
    public function inherit($type, $attributes, $baseStyle, $hoverStyle, $dynamicStyles, $styles): array
    {

        $s1 = $baseStyle;
        $s2 = $hoverStyle;
        $s1options = $styles[$s1];
        $s2options = $styles[$s2];

        foreach ($dynamicStyles as $targetAttribute => $content) {

            if (isset($attributes[$targetAttribute],
                $dynamicStyles[$targetAttribute][$attributes[$targetAttribute]])
            ) {

                if (isset($content[$attributes[$targetAttribute]]['base_style'][$type])) {
                    $styleContent = $content[$attributes[$targetAttribute]]['base_style'][$type];

                    $newBaseStyle = $s1 . $styleContent['code'];
                    $s1options = array_merge($s1options, $styleContent['content']);
                    $s1 = $newBaseStyle;
                }

                if (isset($content[$attributes[$targetAttribute]]['hover_style'][$type])) {
                    $styleContent = $content[$attributes[$targetAttribute]]['hover_style'][$type];

                    $newBaseStyle = $s2 . $styleContent['code'];
                    $s2options = array_merge($s2options, $styleContent['content']);
                    $s2 = $newBaseStyle;
                }
            }
        }

        $result = [];

        if ($s1 !== $baseStyle) {
            $result['base_style_code'] = $s1;
            $result['base_style_content'] = $s1options;
        }

        if ($s2 !== $hoverStyle) {
            $result['hover_style_code'] = $s2;
            $result['hover_style_content'] = $s2options;
        }

        return $result;
    }
}
