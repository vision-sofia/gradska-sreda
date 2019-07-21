<?php

namespace App\DataFixtures\Geospatial;

use App\AppMain\Entity\Geospatial\StyleCondition;
use App\Services\Constant;
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
            $styleCondition->setDescription($item['description'] ?? '');
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
                'attribute' => '_tc',
                'value' => 'sgr',
                'description' => 'Строителна граница',
                'is_dynamic' => false,
                'priority' => 1,
                'base_style' => [
                    Constant::GEOMETRY_TYPE_LINESTRING => [
                        'code' => bin2hex(random_bytes(2)),
                        'content' => [
                            'color' => '#5655a4',
                            'weight' => 7,
                            'opacity' => 0.9,
                        ],
                    ],
                ],
                'hover_style' => [
                    Constant::GEOMETRY_TYPE_LINESTRING => [
                        'code' => bin2hex(random_bytes(2)),
                        'content' => [
                            'weight' => 10,
                            'opacity' => 1,
                        ],
                    ],
                ],
            ],
            [
                'attribute' => '_sca',
                'value' => 'Пешеходни отсечки',
                'description' => 'Обект от категория "пешеходна отсечка"',
                'is_dynamic' => false,
                'priority' => 1,
                'base_style' => [
                    Constant::GEOMETRY_TYPE_LINESTRING => [
                        'code' => 'a1',
                        'content' => [
                            'color' => '#C08055',
                            'weight' => 7,
                            'opacity' => 0.9,
                        ],
                    ],
                ],
                'hover_style' => [
                    Constant::GEOMETRY_TYPE_LINESTRING => [
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
                    Constant::GEOMETRY_TYPE_LINESTRING => [
                        'code' => 'a4',
                        'content' => [
                            'color' => '#F6AE7B',
                            'weight' => 7,
                        ],
                    ],
                ],
                'hover_style' => [
                    Constant::GEOMETRY_TYPE_LINESTRING => [
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
                    Constant::GEOMETRY_TYPE_LINESTRING => [
                        'code' => 'a7',
                        'content' => [
                            'color' => '#F6AE7B',
                            'weight' => 7,
                        ],
                    ],
                ],
                'hover_style' => [
                    Constant::GEOMETRY_TYPE_LINESTRING => [
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
                'description' => 'Географски обекти които са цел на проучване',
                'is_dynamic' => false,
                'priority' => 4,
                'base_style' => [
                    Constant::GEOMETRY_TYPE_LINESTRING => [
                        'code' => 'op',
                        'content' => [
                            'weight' => 4,
                            'opacity' => 0.9,
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
                    Constant::GEOMETRY_TYPE_POINT => [
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
                    Constant::GEOMETRY_TYPE_POINT => [
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
                    Constant::GEOMETRY_TYPE_POINT => [
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
                    Constant::GEOMETRY_TYPE_POINT => [
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
                    Constant::GEOMETRY_TYPE_POINT => [
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
                    Constant::GEOMETRY_TYPE_LINESTRING => [
                        'code' => 'def-line-b',
                        'content' => [
                            'color' => '#ff99ff',
                            'opacity' => 0.6,
                            'weight' => 5,
                        ],
                    ],
                    Constant::GEOMETRY_TYPE_POLYGON => [
                        'code' => 'def-poly-b',
                        'content' => [
                            'color' => '#5655a4',
                            'weight' => 2,
                            'opacity' => 1,
                            'fillOpacity' => 0,
                        ],
                    ],
                ],
                'hover_style' => [
                    Constant::GEOMETRY_TYPE_POINT => [
                        'code' => 'def-point-h',
                        'content' => [
                            'fillColor' => '#ff00ff',
                            'fillOpacity' => 1,
                        ],
                    ],
                    Constant::GEOMETRY_TYPE_LINESTRING => [
                        'code' => 'def-line-h',
                        'content' => [
                            'opacity' => 1,
                            'color' => '#00ffff',
                        ],
                    ],
                    Constant::GEOMETRY_TYPE_POLYGON => [
                        'code' => 'def-poly-h',
                        'content' => [
                            'weight' => 7,

                        ],
                    ],
                ],
            ],
            [
                'attribute' => '_completed',
                'value' => 0,
                'description' => 'Обект с частично попълнена анкета',
                'is_dynamic' => true,
                'priority' => 1,
                'base_style' => [
                    Constant::GEOMETRY_TYPE_LINESTRING => [
                        'code' => bin2hex(random_bytes(2)),
                        'content' => [
                            'color' => '#000000',
                            'weight' => 1,
                            'opacity' => 1,
                        ],
                    ],
                ],
                'hover_style' => [
                    Constant::GEOMETRY_TYPE_LINESTRING => [
                        'code' => bin2hex(random_bytes(2)),
                        'content' => [
                            'weight' => 1,
                            'opacity' => 1,
                        ],
                    ],
                ],
            ],
            [
                'attribute' => '_completed',
                'value' => 1,
                'description' => 'Обект с изцяло попълнена анкета',
                'is_dynamic' => true,
                'priority' => 1,
                'base_style' => [
                    Constant::GEOMETRY_TYPE_LINESTRING => [
                        'code' => bin2hex(random_bytes(2)),
                        'content' => [
                            'color' => '#00FF00',
                            'weight' => 1,
                            'opacity' => 1,
                        ],
                    ],
                ],
                'hover_style' => [
                    Constant::GEOMETRY_TYPE_LINESTRING => [
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
                    Constant::GEOMETRY_TYPE_LINESTRING => [
                        'code' => bin2hex(random_bytes(2)),
                        'content' => [
                            'color' => '#00FF00',
                            'weight' => 7,
                            'opacity' => 1,
                        ],
                    ],
                ],
                'hover_style' => [
                    Constant::GEOMETRY_TYPE_LINESTRING => [
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
