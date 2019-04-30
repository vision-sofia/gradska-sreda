<?php

namespace App\DataFixtures\Geospatial;

use App\AppMain\Entity\Geospatial\Simplify;
use App\Doctrine\ValueObject\IntRange;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class GeometrySimplifyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->data() as $item) {
            $simplify = new Simplify();
            $simplify->setZoom(new IntRange($item['max_zoom'], $item['min_zoom']));
            $simplify->setTolerance($item['tolerance']);

            $manager->persist($simplify);
        }

        $manager->flush();
    }

    private function data(): array
    {
        return [
            [
                'tolerance' => 0.000001,
                'min_zoom' => 21,
                'max_zoom' => 16,
            ], [
                'tolerance' => 0.000002,
                'min_zoom' => 16,
                'max_zoom' => 14,
            ], [
                'tolerance' => 0.00001,
                'min_zoom' => 14,
                'max_zoom' => 10,
            ], [
                'tolerance' => 0.0001,
                'min_zoom' => 10,
                'max_zoom' => 1,
            ],
        ];
    }
}
