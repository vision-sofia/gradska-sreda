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
            $objectType->setCode('');
            $objectType->setStyles($item['style_body']);
            $objectType->setType($item['style_type']);
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
                'priority' => 1,
                'style_type' => 'hover',
                'style_body' => [
                    'line' => [
                        'code' => 'ha1',
                        'content' => [
                            'color' => '#0099ff',
                            'weight' => 10,
                            'opacity' => 1,
                        ],
                    ],
                ],
            ],
            [
                'attribute' => '_sca',
                'value' => 'Пешеходни отсечки',
                'priority' => 1,
                'style_type' => 'base',
                'style_body' => [
                    'line' => [
                        'code' => 'a1',
                        'content' => [
                            'color' => '#0099ff',
                            'weight' => 7,
                            'opacity' => 0.9,
                        ],
                    ],
                ],
            ],
            [
                'attribute' => '_sca',
                'value' => 'Алеи',
                'priority' => 2,
                'style_type' => 'base',
                'style_body' => [
                    'line' => [
                        'code' => 'a4',
                        'content' => [
                            'color' => '#33cc33',
                            'weight' => 7,
                        ],
                    ],
                ],
            ],
            [
                'attribute' => '_sca',
                'value' => 'Пресичания',
                'priority' => 3,
                'style_type' => 'base',
                'style_body' => [
                    'line' => [
                        'code' => 'a7',
                        'content' => [
                            'color' => '#ff3300',
                            'weight' => 7,
                        ],
                    ],
                ],
            ],
            [
                'attribute' => '_behavior',
                'value' => 'survey',
                'priority' => 4,
                'style_type' => 'base',
                'style_body' => [
                    'line' => [
                        'code' => 'op',
                        'content' => [
                            'weight' => 8,
                            'opacity' => 0.7,
                        ],
                    ],
                ],
            ],
        ];
    }
}
