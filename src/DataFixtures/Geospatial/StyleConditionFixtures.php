<?php

namespace App\DataFixtures\Geospatial;

use App\AppMain\Entity\Geospatial\StyleCondition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class StyleConditionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->data() as $item) {
            $objectType = new StyleCondition();
            $objectType->setAttribute($item['attribute']);
            $objectType->setValue($item['value']);

            if (isset($item['base_style'])) {
                $objectType->setBaseStyle($item['base_style']);
            }

            if (isset($item['hover_style'])) {
                $objectType->setHoverStyle($item['hover_style']);
            }

            $objectType->setPriority($item['priority']);

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
                'base_style' => [
                    'line' => [
                        'code' => 'a1',
                        'content' => [
                            'color' => '#0099ff',
                            'weight' => 7,
                            'opacity' => 0.9,
                        ],
                    ],
                ],
                'hover_style' => [
                    'line' => [
                        'code' => 'a1-h',
                        'content' => [
                            'weight' => 10,
                            'opacity' => 1,
                        ],
                    ],
                ],
            ],
            [
                'attribute' => '_sca',
                'value' => 'Алеи',
                'priority' => 2,
                'base_style' => [
                    'line' => [
                        'code' => 'a4',
                        'content' => [
                            'color' => '#33cc33',
                            'weight' => 7,
                        ],
                    ],
                ],
                'hover_style' => [
                    'line' => [
                        'code' => 'a4-h',
                        'content' => [
                            'weight' => 10,
                            'opacity' => 1,
                        ],
                    ],
                ],
            ],
            [
                'attribute' => '_sca',
                'value' => 'Пресичания',
                'priority' => 3,
                'base_style' => [
                    'line' => [
                        'code' => 'a7',
                        'content' => [
                            'color' => '#ff3300',
                            'weight' => 7,
                        ],
                    ],
                ],
                'hover_style' => [
                    'line' => [
                        'code' => 'a7-h',
                        'content' => [
                            'weight' => 10,
                            'opacity' => 1,
                        ],
                    ],
                ],
            ],
            [
                'attribute' => '_behavior',
                'value' => 'survey',
                'priority' => 4,
                'base_style' => [
                    'line' => [
                        'code' => 'op',
                        'content' => [
                            'weight' => 8,
                            'opacity' => 0.7,
                        ],
                    ],
                ],
            ],
            [
                'attribute' => 'has_vhc_metro',
                'value' => 1,
                'priority' => 1,
                'base_style' => [
                    'point' => [
                        'code' => 'vhc-m-b',
                        'content' => [
                            'radius' => 8,
                            'color' => '#000',
                            'weight' => 1,
                            'opacity' => 1,
                            'fillColor' => '#3847A4',
                            'fillOpacity' => 0.8,
                        ],
                    ],
                ],
                'hover_style' => [
                    'point' => [
                        'code' => 'vhc-m-h',
                        'content' => [
                            'fillColor' => '#00FFF0',
                        ],
                    ],
                ],
            ],
            [
                'attribute' => 'has_vhc_other',
                'value' => 1,
                'priority' => 1,
                'base_style' => [
                    'point' => [
                        'code' => 'vhc-o-b',
                        'content' => [
                            'radius' => 6,
                            'color' => '#000',
                            'weight' => 1,
                            'opacity' => 1,
                            'fillColor' => '#4296EC',
                            'fillOpacity' => 0.8,
                        ],
                    ],
                ],
                'hover_style' => [
                    'point' => [
                        'code' => 'vhc-o-h',
                        'content' => [
                            'fillColor' => '#00FF0F',
                        ],
                    ],
                ],
            ],
            [
                'attribute' => '_default',
                'value' => '',
                'priority' => 0,
                'base_style' => [
                    'point' => [
                        'code' => 'def-point-base',
                        'content' => [
                            'radius' => 10,
                            'color' => '#000',
                            'weight' => 1,
                            'opacity' => 1,
                            'fillColor' => '#ff7800',
                            'fillOpacity' => 0.8,
                        ],
                    ],
                ],
                'hover_style' => [
                    'point' => [
                        'code' => 'def-point-hover',
                        'content' => [
                            'fillColor' => '#ff00ff',
                            'fillOpacity' => 1,
                        ],
                    ],
                ],
            ],
        ];
    }
}
