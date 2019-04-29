<?php

namespace App\Tests\Services\Geospatial\StyleBuilder;

use App\Services\Geospatial\Style;
use PHPUnit\Framework\TestCase;

class StyleTest extends TestCase
{
    public function testConvertStyleToText(): void
    {
        $styleArray = [
            'color' => '#FF00FF0',
            'opacity' => 0.9,
            'fill' => false
        ];

        $expect = '';
        $expect .= "color: \"#FF00FF0\",\n";
        $expect .= "opacity: 0.9,\n";
        $expect .= "fill: false,\n";

        $styleService = new Style();

        $this->assertEquals($expect, $styleService->styleToText($styleArray));
    }

    public function testConvertTextToGroupStyle(): void
    {
        $text = '';
        $text .= "color: \"#FF00FF0\",\n";
        $text .= "opacity: 0.9,\n";
        $text .= "fill: false,\n";

        $expect = [
            'color' => '#FF00FF0',
            'opacity' => 0.9,
            'fill' => false
        ];

        $styleService = new Style();

        $this->assertEquals($expect, $styleService->textToGroupStyle($text));
    }
}
