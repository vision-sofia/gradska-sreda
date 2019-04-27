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
            $objectType->setDescription($item['description']);
            $objectType->setIsForInternalSystem(true);

            $manager->persist($objectType);
        }

        $manager->flush();
    }

    private function data(): array
    {
        return [
            [
                'code' => 'on_dialog_line',
                'description' => 'Селекция на обект тип линия',
                'style' => [
                    'color' => '#00ffff',
                    'opacity' => 0.5,
                ],
            ], [
                'code' => 'on_dialog_point',
                'description' => 'Селекция на обект тип точка',
                'style' => [
                    'fillColor' => '#00ffff',
                    'opacity' => 0.5,
                ],
            ], [
                'code' => 'on_dialog_polygon',
                'description' => 'Селекция на обект тип полигон',
                'style' => [
                    'fillColor' => '#00ffff',
                    'opacity' => 0.5,
                ],
            ], [
                'code' => 'gc_bbox',
                'description' => 'Bounding box на маршрут',
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
