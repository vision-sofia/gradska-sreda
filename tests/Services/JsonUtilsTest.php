<?php

namespace App\Tests\Services\Geospatial\StyleBuilder;

use App\Services\JsonUtils;
use PHPUnit\Framework\TestCase;

class JsonUtilsTest extends TestCase
{
    /** @var JsonUtils */
    protected $jsonUtils;

    protected function setUp(): void
    {
        $this->jsonUtils = new JsonUtils();
    }

    public function testConcatJsonString(): void
    {
        $expected = json_encode([
            'default_zoom' => 17,
            'geometry' => ['b' => 'b'],
        ]);

        $data = [
            'default_zoom' => 17,
        ];

        $result = $this->jsonUtils->concatString($data, 'geometry', json_encode(['b' => 'b']));

        $this->assertEquals($expected, $result);
    }

    public function testWrap(): void
    {
        $data = [
            'a',
            'b',
            'c',
        ];

        $result = $this->jsonUtils->joinArray($data);

        $this->assertEquals('[a,b,c]', $result);
    }
}
