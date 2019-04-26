<?php

namespace App\Tests\Services\Geospatial\StyleBuilder;

use App\Services\Geospatial\StyleBuilder\StyleUtils;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function testStyleInheritance()
    {
        $s = new StyleUtils();

        $styles = [
            's' => [
                'color' => '#FFF',
                'opacity' => 0.5
            ],
            'h' => [
                'color' => '#FFF',
                'opacity' => 0.5
            ]/*,
            'c' => [
                'color' => '#FF0',
                'opacity' => 0.5
            ]*/
        ];

        $dynamicStyle = [
            '_gc' => [
                1 => [
                    'base_style' => [
                        'line' => [
                            'code' => 'ab',
                            'content' => [
                                'color' => '#00FF00',
                                'weight' => 1,

                            ],
                        ],
                    ],
                    'hover_style' => [
                        'line' => [
                            'code' => 'cd',
                            'content' => [
                                'color' => '#00FF00',
                                'weight' => 1,
                            ],
                        ],
                    ],
                ]
            ]
        ];

        $geoObjectAttributes = [
            '_s1' => 's',
            '_s2' => 'h',
            '_gc' => 1,
         #   '_gg' => 1,
        ];


        $result = $s->inherit('line', $geoObjectAttributes, $dynamicStyle, $styles);

        $expect = [
            'base_style_code' => 'sab',
            'hover_style_code' => 'hcd',
            'base_style_content' => [
                'color' => '#00FF00',
                'weight' => 1,
                'opacity' => 0.5,
            ],
            'hover_style_content' => [
                'color' => '#00FF00',
                'weight' => 1,
                'opacity' => 0.5,
            ],
        ];


        $this->assertEquals($expect, $result);
    }
}
