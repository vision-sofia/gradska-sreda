<?php

namespace App\Services\Geospatial\StyleBuilder;

class StyleUtils
{
    private array $staticStyles;
    private array $dynamicStyles;

    public function getStaticStyles()
    {
        return $this->staticStyles;
    }

    public function setStaticStyles(array $staticStyles): void
    {
        $this->staticStyles = $staticStyles;
    }

    public function getDynamicStyles()
    {
        return $this->dynamicStyles;
    }

    public function setDynamicStyles(array $dynamicStyles): void
    {
        $this->dynamicStyles = $dynamicStyles;
    }

    public function inherit(string $geometryType, object $attributes, string $baseStyle, string $hoverStyle): array
    {
        $s1 = $baseStyle;
        $s2 = $hoverStyle;

        $s1options = '';
        $s2options = '';

        if (isset($this->staticStyles[$baseStyle], $this->staticStyles[$hoverStyle])) {
            $s1options = $this->staticStyles[$s1];
            $s2options = $this->staticStyles[$s2];

            foreach ($this->dynamicStyles as $targetAttribute => $content) {
                $target = isset($attributes->{$targetAttribute}) ? $attributes->{$targetAttribute} : null;

                if (isset($attributes->{$targetAttribute}, $this->dynamicStyles[$targetAttribute][$target])) {
                    if (isset($content[$target]['base_style'][$geometryType])) {
                        $style = $content[$target]['base_style'][$geometryType];

                        $s1options = array_merge($s1options, $style['content']);
                        $s1 .= $style['code'];
                    }

                    if (isset($content[$target]['hover_style'][$geometryType])) {
                        $style = $content[$target]['hover_style'][$geometryType];

                        $s2options = array_merge($s2options, $style['content']);
                        $s2 .= $style['code'];
                    }
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
