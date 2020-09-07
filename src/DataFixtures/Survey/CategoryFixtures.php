<?php

namespace App\DataFixtures\Survey;

use App\AppMain\Entity\Geospatial\ObjectType;
use App\AppMain\Entity\Survey\Evaluation\Subject;
use App\AppMain\Entity\Survey\Survey\Category;
use App\AppMain\Entity\Survey\Survey\Element;
use App\AppMain\Entity\Survey\Survey\Survey;
use App\DataFixtures\Geospatial\ObjectTypeFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            ObjectTypeFixtures::class,
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
                        $criterionObject = new Subject\Criterion();
                        $criterionObject->setName($criterion['title']);
                        $criterionObject->setGroup($categoryObject);

                        $manager->persist($criterionObject);
                        $manager->flush();

                        if (isset($criterion['indicators'])) {
                            foreach ($criterion['indicators'] as $indicator) {
                                $indicatorObject = new Subject\Indicator();
                                $indicatorObject->setName($indicator);
                                $indicatorObject->setCriterion($criterionObject);

                                $manager->persist($indicatorObject);
                                $manager->flush();
                            }
                        }
                    }

                    foreach ($category['object_types'] as $name) {
                        /** @var ObjectType|null $objectType */
                        $objectType = $manager->getRepository(ObjectType::class)
                                              ->findOneBy(['name' => $name])
                        ;

                        if (!$objectType) {
                            continue;
                        }

                        $element = new Element();
                        $element->setCategory($categoryObject);
                        $element->setObjectType($objectType);

                        $manager->persist($element);
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
                'survey' => 'Анкета',
                'category' => [
                    [
                        'name' => 'Пешеходни отсечки',
                        'parent' => null,
                        'object_types' => [
                            'Тротоар',
                        ],
                        'criteria' => [
                            [
                                'title' => 'Достъпност и проходимост',
                                'indicators' => [
                                    'Основен',
                                    'Удобства за хора със затруднено придвижване (скосени бордюри, тактилни плочки, звукова сигнализация за пресечки)',
                                    'Физически препятствия (паркирали коли, маси на заведения, кофи за боклук, спирки и други.)',
                                    'Наличие на достатъчно пешеходно пространство спрямо наличните пешеходни потоци',
                                    'Наличие на конфликти с други форми на придвижване.',
                                ],
                            ],
                            [
                                'title' => 'Сигурност',
                                'indicators' => [
                                    'Сигурност срещу престъпления и насилие (добра осветеност, липса на изоставени обекти и сгради, ‘светли’ приземни етажи, оживеност и денонощни обекти)',
                                    'Наличие на елементи осигуряващи защита срещу неприятни сензорни усещания (вятър, дъжд, горещина, замърсяване и шум)',
                                    'Задоволителна защита от градския трафик',
                                ],
                            ],
                            [
                                'title' => 'Качество на настилката',
                                'indicators' => [
                                    'Наличие на настилка, дупки, подвижни плочки и/или неравности',
                                    'Хлъзгавост на настилката',
                                    'Добро отводняване при дъжд',
                                    'Добра поддръжка и чистота на пешеходната настилка',
                                ],
                            ],
                            [
                                'title' => 'Комфорт и привлекателност',
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
                        'name' => 'Алеи',
                        'parent' => null,
                        'object_types' => [
                            'Алея с настилка',
                            'Алея без настилка',
                            'Алея',
                        ],
                        'criteria' => [
                            [
                                'title' => 'Достъпност и проходимост',
                            ],
                            [
                                'title' => 'Сигурност',
                            ],
                            [
                                'title' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Пресичания',
                        'parent' => null,
                        'object_types' => [
                            'Нерегулирано',
                            'Пешеходна пътека',
                            'Светофар',
                            'Подлез',
                            'Стълбище',
                        ],
                        'criteria' => [
                            [
                                'title' => 'Достъпност и проходимост',
                                'indicators' => [
                                    'Ясно регулирано пресичане (наличие на пешеходна пътека или светофар)',
                                    'Удобства за хора със затруднено придвижване (скосени бордюри, тактилни плочки, звукова сигнализация)',
                                    'Физически препятствия (паркирани коли, маси на заведения, спирки, кофи за боклук и други)',
                                ],
                            ],
                            [
                                'title' => 'Сигурност',
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
            [
                'survey' => 'Анкета-2',
                'category' => [
                    [
                        'name' => 'Категория-1',
                        'parent' => null,
                        'object_types' => [
                            'Алея с настилка',
                            'Алея без настилка',
                            'Алея',
                        ],
                        'criteria' => [
                            [
                                'title' => 'Критерии-1',
                            ],
                            [
                                'title' => 'Критерии-2',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
