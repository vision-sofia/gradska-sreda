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
                'description' => 'Click и избиране на обект тип линия',
                'style' => [
                    'color' => '#00ffff',
                    'opacity' => 0.5,
                ],
            ], [
                'code' => 'on_dialog_point',
                'description' => 'Click и избиране на обект тип точка',
                'style' => [
                    'fillColor' => '#00ffff',
                    'opacity' => 0.5,
                ],
            ], [
                'code' => 'on_dialog_polygon',
                'description' => 'Click и избиране на обект тип полигон',
                'style' => [
                    'fillColor' => '#00ffff',
                    'opacity' => 0.5,
                ],
            ]
        ];
    }
}
