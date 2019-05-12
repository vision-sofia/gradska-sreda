<?php

namespace App\Tests\Services\Geospatial\StyleBuilder;

use App\Services\Geospatial\StyleBuilder\StyleUtils;
use PHPUnit\Framework\TestCase;

class StyleUtilsTest extends TestCase
{
    public function testStyleInheritance(): void
    {
        $staticStyles = [
            's' => [
                'color' => '#FFF',
                'opacity' => 0.4
            ],
            'h' => [
                'color' => '#FFF',
                'opacity' => 0.7
            ],
            'c' => [
                'color' => '#FF0',
                'opacity' => 0.5
            ]
        ];

        $dynamicStyles = [
            '_gc' => [
                1 => [
                    'base_style' => [
                        'line' => [
                            'code' => 'ab',
                            'content' => [
                                'color' => '#00FF00',
                                'weight' => 1,
                                'opacity' => 0.1

                            ],
                        ],
                    ],
                    'hover_style' => [
                        'line' => [
                            'code' => 'cd',
                            'content' => [
                                'color' => '#00FF00',
                                'weight' => 1,
                                'opacity' => 0.2
                            ],
                        ],
                    ],
                ]
            ]
        ];

        $geoObjectAttributes = new \StdClass();
        $geoObjectAttributes->_s1 = 's';
        $geoObjectAttributes->_s2 = 'h';
        $geoObjectAttributes->_gc = 1;
        $geoObjectAttributes->_gg = 1;

        $s = new StyleUtils();
        $s->setDynamicStyles($dynamicStyles);
        $s->setStaticStyles($staticStyles);

        $result = $s->inherit('line', $geoObjectAttributes, 's', 'h');

        $expect = [
            'base_style_code' => 'sab',
            'hover_style_code' => 'hcd',
            'base_style_content' => [
                'color' => '#00FF00',
                'weight' => 1,
                'opacity' => 0.1,
            ],
            'hover_style_content' => [
                'color' => '#00FF00',
                'weight' => 1,
                'opacity' => 0.2,
            ],
        ];

        $this->assertEquals($expect, $result);
    }
}
