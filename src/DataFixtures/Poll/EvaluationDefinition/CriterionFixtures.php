<?php

namespace App\DataFixtures\Poll\EvaluationDefinition;

use App\AppMain\Entity\SurveySystem\Evaluation\Criterion;
use App\AppMain\Entity\SurveySystem\Evaluation\Indicator;
use App\AppMain\Entity\SurveySystem\Survey\Survey;
use App\DataFixtures\Poll\Poll\LoadPollFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CriterionFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            LoadPollFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->data() as $value) {

            $survey = $manager->getRepository(Survey::class)->findOneBy(['name' => $value['survey']]);

            if ($survey) {
                foreach ($value['criteria'] as $criteria) {
                    $criterion = new Criterion();
                    $criterion->setName($criteria['title']);
                    $criterion->setSurvey($survey);

                    $manager->persist($criterion);
                    $manager->flush();

                    foreach ($criteria['indicators'] as $indicator) {
                        $indicatorObject = new Indicator();
                        $indicatorObject->setName($indicator);
                        $indicatorObject->setCriterion($criterion);

                        $manager->persist($indicatorObject);
                        $manager->flush();
                    }
                }
            }

        }

        $manager->flush();
    }

    private function data(): array
    {
        return [
            [
                'survey'   => 'Анкета',
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
        ];
    }
}
