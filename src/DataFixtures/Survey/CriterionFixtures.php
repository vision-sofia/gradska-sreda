<?php

namespace App\DataFixtures\Survey;

use App\AppMain\Entity\Geospatial\Layer;
use App\AppMain\Entity\SurveySystem\Evaluation\Criterion;
use App\AppMain\Entity\SurveySystem\Evaluation\Indicator;
use App\AppMain\Entity\SurveySystem\Survey\Category;

use App\AppMain\Entity\SurveySystem\Survey\Survey;
use App\DataFixtures\Geospatial\LayerFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CriterionFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            LayerFixtures::class,
            SurveyFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->data() as $value) {

            $survey = $manager->getRepository(Survey::class)->findOneBy(['name' => $value['survey']]);

            if ($survey) {
                foreach ($value['category'] as $category) {
                    $categoryObject = new Category();
                    $categoryObject->setName($category['name']);
                    $categoryObject->setSurvey($survey);

                    $parent = $manager->getRepository(Category::class)
                                      ->findOneBy(['name' => $category['parent']])
                    ;

                    $categoryObject->setParent($parent);

                    $manager->persist($categoryObject);
                    $manager->flush();

                    foreach ($category['criteria'] as $criterion) {
                        $criterionObject = new Criterion();
                        $criterionObject->setName($criterion['title']);
                        $criterionObject->setGroup($categoryObject);

                        $manager->persist($criterionObject);
                        $manager->flush();

                        foreach ($criterion['indicators'] as $indicator) {
                            $indicatorObject = new Indicator();
                            $indicatorObject->setName($indicator);
                            $indicatorObject->setCriterion($criterionObject);

                            $manager->persist($indicatorObject);
                            $manager->flush();
                        }

                    }

                    foreach ($category['layers'] as $name) {

                        $layer = $manager->getRepository(Layer::class)
                                         ->findOneBy(['name' => $name])
                        ;

                        $surveyLayer = new \App\AppMain\Entity\SurveySystem\Survey\Layer();
                        $surveyLayer->setCategory($categoryObject);
                        $surveyLayer->setLayer($layer);

                        $manager->persist($surveyLayer);
                        $manager->flush();

                    }
                }
            }
        }
    }

    private function data(): array
    {
        return [
            [
                'survey'   => 'Анкета',
                'category' => [
                    [
                        'name'     => 'пешеходна отсечка',
                        'parent'   => null,
                        'layers'   => [
                            'тротоар',
                            'алея',
                        ],
                        'criteria' => [
                            [
                                'title'      => 'Достъпност и проходимост',
                                'indicators' => [
                                    'Удобства за хора със затруднено придвижване (скосени бордюри, тактилни плочки, звукова сигнализация за пресечки)',
                                    'Физически препятствия (паркирали коли, маси на заведения, кофи за боклук, спирки и други.)',
                                    'Наличие на достатъчно пешеходно пространство спрямо наличните пешеходни потоци',
                                    'Наличие на конфликти с други форми на придвижване.',
                                ],
                            ],
                            [
                                'title'      => 'Сигурност',
                                'indicators' => [
                                    'Сигурност срещу престъпления и насилие (добра осветеност, липса на изоставени обекти и сгради, ‘светли’ приземни етажи, оживеност и денонощни обекти)',
                                    'Наличие на елементи осигуряващи защита срещу неприятни сензорни усещания (вятър, дъжд, горещина, замърсяване и шум)',
                                    'Задоволителна защита от градския трафик',
                                ],
                            ],
                            [
                                'title'      => 'Качество на настилката',
                                'indicators' => [
                                    'Наличие на настилка, дупки, подвижни плочки и/или неравности',
                                    'Хлъзгавост на настилката',
                                    'Добро отводняване при дъжд',
                                    'Добра поддръжка и чистота на пешеходната настилка',
                                ],
                            ],
                            [
                                'title'      => 'Комфорт и привлекателност',
                                'indicators' => [
                                    'Оживена градска среда, активни и привлекателни приземни етажи на прилежащите сгради',
                                    'Архитектура, градско обзавеждане и естетика (привлекателен дизайн, добро състояние на фасади и сгради с висока архитектурна стойност)',
                                    'Позитивни сензорни усещания (ниска честота на преминаващи автомобили, чистота на средата и въздуха)',
                                    'Наличие на озеленяване',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name'     => 'Тротоари',
                        'parent'   => 'пешеходна отсечка',
                        'layers'   => [
                            'тротоар',
                        ],
                        'criteria' => [],
                    ],
                    [
                        'name'     => 'Алеи',
                        'parent'   => 'пешеходна отсечка',
                        'layers'   => [
                            'алея',
                        ],
                        'criteria' => [],
                    ],
                    [
                        'name'     => 'Пресичания',
                        'parent'   => null,
                        'layers'   => [
                            'пресичане',
                        ],
                        'criteria' => [
                            [
                                'title'      => 'Достъпност и проходимост',
                                'indicators' => [
                                    'Ясно регулирано пресичане (наличие на пешеходна пътека или светофар)',
                                    'Удобства за хора със затруднено придвижване (скосени бордюри, тактилни плочки, звукова сигнализация)',
                                    'Физически препятствия (паркирани коли, маси на заведения, спирки, кофи за боклук и други)',
                                ],
                            ],
                            [
                                'title'      => 'Сигурност',
                                'indicators' => [
                                    'Ясно регулирано пресичане (наличие на пешеходна пътека или светофар)',
                                    'Физически препятствия (паркирани коли, маси на заведения, спирки, кофи за боклук и други',
                                    'Наличие на скосени бордюри / повдигната пешеходна повърхност',
                                    'Добра обозначеност и осветеност',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
