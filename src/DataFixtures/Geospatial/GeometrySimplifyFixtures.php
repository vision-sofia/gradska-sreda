<?php

namespace App\DataFixtures\Geospatial;

use App\AppMain\Entity\Geospatial\Simplify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class GeometrySimplifyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->data() as $item) {
            $simplify = new Simplify();
            $simplify->setMinZoom($item['min_zoom']);
            $simplify->setMaxZoom($item['max_zoom']);
            $simplify->setTolerance($item['tolerance']);

            $manager->persist($simplify);
            $manager->flush();
        }
    }

    private function data(): array
    {
        return [
            [
                'tolerance' => 0.0001,
                'min_zoom' => 20,
                'max_zoom' => 16,
            ], [
                'tolerance' => 0.0002,
                'min_zoom' => 15.9,
                'max_zoom' => 10,
            ], [
                'tolerance' => 0.0003,
                'min_zoom' => 9.9,
                'max_zoom' => 1,
            ],
        ];
    }
}
