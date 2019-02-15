<?php

namespace App\Tests\Services\Geometry;

use App\Services\Geometry\Utils;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class UtilsTest extends TestCase
{
    public function testFindSimplifyTolerance(): void
    {
        $data = [
            [
                'min_zoom' => 16,
                'max_zoom' => 14,
                'tolerance' => 0.00001,
            ],
            [
                'min_zoom' => 14,
                'max_zoom' => 12,
                'tolerance' => 0.00002,
            ],
            [
                'min_zoom' => 12,
                'max_zoom' => 10,
                'tolerance' => 0.00003,
            ],
        ];

        $utils = new Utils();

        $result = $utils->findTolerance($data, 12);

        $this->assertSame(0.00002, $result);
    }
}
