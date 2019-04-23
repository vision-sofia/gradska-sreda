<?php

namespace App\DataFixtures\Geospatial;

use App\AppMain\Entity\Geospatial\StyleGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class StyleGroupFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->data() as $item) {
            $objectType = new StyleGroup();
            $objectType->setCode($item['code']);
            $objectType->setStyle($item['style']);

            $manager->persist($objectType);
        }

        $manager->flush();
    }

    private function data(): array
    {
        return [
            [
                'code' => 'on_dialog_line',
                'style' => [
                    'color' => '#00ffff',
                    'opacity' => 0.5,
                ],
            ], [
                'code' => 'on_dialog_point',
                'style' => [
                    'fillColor' => '#00ffff',
                    'opacity' => 0.5,
                ],
            ], [
                'code' => 'on_dialog_polygon',
                'style' => [
                    'fillColor' => '#00ffff',
                    'opacity' => 0.5,
                ],
            ], [
                'code' => 'upr-c',
                'style' => [
                    'color' => '#000',
                    'weight' => 2,
                ],
            ], [
                'code' => 'upr-uc',
                'style' => [
                    'color' => '#000',
                    'weight' => 1,
                ],
            ], [
                'code' => 'gc_bounding_box',
                'style' => [
                    'color' => '#FF0000',
                    'weight' => 1,
                    'opacity' => 1,
                ],
            ], [
                'code' => 'gc_mark',
                'style' => [
                    'color' => '#FF0000',
                    'weight' => 1,
                    'opacity' => 1,
                ],
            ]
        ];
    }
}
