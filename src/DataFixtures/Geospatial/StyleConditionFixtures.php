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
            $styleCondition = new StyleCondition();
            $styleCondition->setAttribute($item['attribute']);
            $styleCondition->setValue($item['value']);
            $styleCondition->setIsDynamic($item['is_dynamic']);
            $styleCondition->setDescription(isset($item['description']) ? $item['description'] : '');
            $styleCondition->setPriority($item['priority']);

            if (isset($item['base_style'])) {
                $styleCondition->setBaseStyle($item['base_style']);
            }

            if (isset($item['hover_style'])) {
                $styleCondition->setHoverStyle($item['hover_style']);
            }

            $manager->persist($styleCondition);
        }

        $manager->flush();
    }

    private function data(): array
    {
        return [
            [
                'attribute' => '_sca',
                'value' => 'Пешеходни отсечки',
                'description' => 'Обект от категория "пешеходна отсечка"',
                'is_dynamic' => false,
                'priority' => 1,
                'base_style' => [
                    'line' => [
                        'code' => 'a1',
                        'content' => [
                            'color' => '#062978',
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
                'description' => 'Обект от категория "алея"',
                'is_dynamic' => false,
                'priority' => 2,
                'base_style' => [
                    'line' => [
                        'code' => 'a4',
                        'content' => [
                            'color' => '#9FA3AD',
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
                'description' => 'Обект от категория "пресичане"',
                'is_dynamic' => false,
                'priority' => 3,
                'base_style' => [
                    'line' => [
                        'code' => 'a7',
                        'content' => [
                            'color' => '#828FAF',
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
                'description' => 'Географски обекти които са обект на проучване',
                'is_dynamic' => false,
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
                'description' => 'Спирка на метро',
                'is_dynamic' => false,
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
                'description' => 'Спирка на наземен градски транспорт',
                'is_dynamic' => false,
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
                'description' => 'Стил по подразбиране',
                'is_dynamic' => false,
                'priority' => 0,
                'base_style' => [
                    'point' => [
                        'code' => 'def-point-b',
                        'content' => [
                            'radius' => 10,
                            'color' => '#000',
                            'weight' => 1,
                            'opacity' => 1,
                            'fillColor' => '#ff7800',
                            'fillOpacity' => 0.8,
                        ],
                    ],
                    'line' => [
                        'code' => 'def-line-b',
                        'content' => [
                            'color' => '#ff99ff',
                            'opacity' => 0.6,
                            'weight' => 5,
                        ],
                    ],
                    'polygon' => [
                        'code' => 'def-poly-b',
                        'content' => [
                            'color'=> '#5655a4',
                            'weight'=> 2,
                            'opacity'=> 1,
                            'fillOpacity'=> 0,
                        ],
                    ],
                ],
                'hover_style' => [
                    'point' => [
                        'code' => 'def-point-h',
                        'content' => [
                            'fillColor' => '#ff00ff',
                            'fillOpacity' => 1,
                        ],
                    ],
                    'line' => [
                        'code' => 'def-line-h',
                        'content' => [
                            'opacity' => 1,
                            'color' => '#00ffff',
                        ],
                    ],
                    'polygon' => [
                        'code' => 'def-poly-h',
                        'content' => [
                            'weight'=> 7,

                        ],
                    ],
                ],
            ],
            [
                'attribute' => '_geo_comp',
                'value' => 0,
                'description' => 'Обект с частично попълнена анкета',
                'is_dynamic' => true,
                'priority' => 1,
                'base_style' => [
                    'line' => [
                        'code' => bin2hex(random_bytes(2)),
                        'content' => [
                            'color' => '#000000',
                            'weight' => 1,
                            'opacity' => 1,
                        ],
                    ],
                ],
                'hover_style' => [
                    'line' => [
                        'code' => bin2hex(random_bytes(2)),
                        'content' => [
                            'weight' => 1,
                            'opacity' => 1,
                        ],
                    ],
                ],
            ],
            [
                'attribute' => '_geo_comp',
                'value' => 1,
                'description' => 'Обект с изцяло попълнена анкета',
                'is_dynamic' => true,
                'priority' => 1,
                'base_style' => [
                    'line' => [
                        'code' => bin2hex(random_bytes(2)),
                        'content' => [
                            'color' => '#00FF00',
                            'weight' => 1,
                            'opacity' => 1,
                        ],
                    ],
                ],
                'hover_style' => [
                    'line' => [
                        'code' => bin2hex(random_bytes(2)),
                        'content' => [
                            'weight' => 1,
                            'opacity' => 1,
                        ],
                    ],
                ],
            ], [
                'attribute' => '_gc',
                'value' => 1,
                'description' => 'Маршрут',
                'is_dynamic' => true,
                'priority' => 1,
                'base_style' => [
                    'line' => [
                        'code' => bin2hex(random_bytes(2)),
                        'content' => [
                            'color' => '#FF00FF',
                            'weight' => 7,
                            'opacity' => 1,
                        ],
                    ],
                ],
                'hover_style' => [
                    'line' => [
                        'code' => bin2hex(random_bytes(2)),
                        'content' => [
                            'color' => '#AA00AA',
                            'weight' => 7,
                        ],
                    ],
                ],
            ]
        ];
    }
}
