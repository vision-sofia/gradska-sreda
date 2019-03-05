<?php

namespace App\DataFixtures\Geospatial;

use App\AppMain\Entity\Geospatial\Style;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class StyleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->data() as $item) {
            $objectType = new Style();
            $objectType->setAttribute($item['attribute']);
            $objectType->setValue($item['value']);
            $objectType->setCode($item['code']);
            $objectType->setStyles($item['styles']);
            $objectType->setType($item['type']);
            $objectType->setPriority($item['priority']);
            $objectType->setStyleValue('');
            $objectType->setStyleOption('');

            $manager->persist($objectType);
        }

        $manager->flush();
    }

    private function data(): array
    {
        return [
            [
                'attribute' => '_sca',
                'value' => 'Пешеходни отсечки',
                'code' => 'a1',
                'type' => 'base_normal',
                'priority' => 1,
                'styles' => [
                    'color' => '#0099ff',
                    'weight' => 7,
                    'opacity' => 0.9,
                ],
            ],
            [
                'attribute' => '_sca',
                'value' => 'Алеи',
                'code' => 'a4',
                'type' => 'base_normal',
                'priority' => 2,
                'styles' => [
                    'color' => '#33cc33',
                    'weight' => 7,
                ],
            ],
            [
                'attribute' => '_sca',
                'value' => 'Пресичания',
                'code' => 'a7',
                'type' => 'base_normal',
                'priority' => 3,
                'styles' => [
                    'color' => '#ff3300',
                    'weight' => 7,
                ],
            ],
            [
                'attribute' => '_behavior',
                'value' => 'survey',
                'code' => 'op',
                'type' => 'base_normal',
                'priority' => 4,
                'styles' => [
                    'weight' => 8,
                    'opacity' => 0.7,
                ],
            ],
        ];
    }
}
