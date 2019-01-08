<?php

namespace App\DataFixtures\Survey;

use App\AppMain\Entity\SurveySystem\Question\Answer;
use App\AppMain\Entity\SurveySystem\Question\Question;
use App\AppMain\Entity\SurveySystem\Survey\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class QuestionFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            CriterionFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->data() as $question) {
            $category = $manager->getRepository(Category::class)
                                ->findOneBy([
                                    'name' => $question['category']
                                ]);

            $questionObject = new Question();
            $questionObject->setCategory($category);
            $questionObject->setTitle($question['question']);
            $questionObject->setHasMultipleAnswers($question['has_multiple_answers']);

            $manager->persist($questionObject);
            $manager->flush();

            foreach ($question['answers'] as $answer) {
                $answerObject = new Answer();
                $answerObject->setTitle($answer['title']);
                $answerObject->setQuestion($questionObject);
                $answerObject->setIsFreeAnswer(false);

                $manager->persist($answerObject);
                $manager->flush();

                if(isset($answer['child'])) {
                    foreach ($answer['child'] as $item) {
                        $answerChild = new Answer();
                        $answerChild->setTitle($item['title']);
                        $answerChild->setQuestion($questionObject);
                        $answerChild->setParent($answerObject);
                        $answerChild->setIsFreeAnswer(isset($item['is_free_answer']) && $item['is_free_answer'] === true);

                        $manager->persist($answerChild);
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
                'category' => 'пресичане',
                'question' => 'Какъв е автомобилният трафик в момента?',
                'has_multiple_answers' => false,
                'answers' => [
                    ['title' => 'Интензивен',],
                    ['title' => 'Умерен',],
                    ['title' => 'Спокоен',],
                ],
            ],
            [
                'category' => 'пресичане',
                'question' => 'Какъв вид е пресичането?',
                'has_multiple_answers' => true,
                'answers' => [
                    ['title' => 'Светофар'],
                    ['title' => 'Пешеходна пътека'],
                    ['title' => 'Пешеходен подлез'],
                    ['title' => 'Пешеходен мост / надлез'],
                    ['title' => 'Нерегулирано (квартални улици)'],
                    ['title' => 'Несъществуващо(!)'],
                ],
            ],
            [
                'category' => 'пресичане',
                'question' => 'Помислено ли е за лица с намалена подвижност, незрящи, детски колички и др?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да',
                        'child' => [
                            ['title' => 'Скосени бордюри / повдигната пешеходна повърхност'],
                            ['title' => 'Тактилни (релефни) плочки'],
                            ['title' => 'Звукова сигнализация'],
                        ]
                    ],
                    [
                        'title' => 'Не, никакви'
                    ],
                ],
            ],
            [
                'category' => 'пешеходна отсечка',
                'question' => 'Има ли някакви препятствия в тази отсечка?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да, по цялото продължение на отсечката',
                        'child' => [
                            ['title' => 'Паркирани коли'],
                            ['title' => 'Кофи за боклук'],
                            ['title' => 'Маси на заведения'],
                            ['title' => 'Спирки на МГТ'],
                            ['title' => 'Несъобразено поставени осветителни стълбове и реклами'],
                            ['title' => 'Антипаркинг колчета'],
                            ['title' => 'Други', 'is_free_answer' => true],
                        ]
                    ],
                    ['title' => 'Да, епизодично'],
                    ['title' => 'Не, никакви'],
                ],
            ],
            [
                'category' => 'пешеходна отсечка',
                'question' => 'Има ли сериозен конфликт с велосипедисти, товарни дейности, скутери, скейтборд или други?',
                'has_multiple_answers' => false,
                'answers' => [
                    ['title' => 'Да'],
                    ['title' => 'Не'],
                ],
            ],
            [
                'category' => 'пешеходна отсечка',
                'question' => 'Има ли проблеми с настилката в момента?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Не, никакви'
                    ],
                    [
                        'title' => 'Да',
                        'child' => [
                            ['title' => 'Има много неравности'],
                            ['title' => 'Липса на настилка'],
                            ['title' => 'Хлъзгаво е'],
                            ['title' => 'Има наводнени участъци'],
                            ['title' => 'Друго', 'is_free_answer' => true],
                        ]
                    ],
                ],
            ],
            [
                'category' => 'пешеходна отсечка',
                'question' => 'Има ли ‘светли’ и активни партерни етажи (наличие на търговски обекти)?',
                'has_multiple_answers' => false,
                'answers' => [
                    ['title' => 'Да'],
                    ['title' => 'Не'],
                ],
            ],
            [
                'category' => 'пешеходна отсечка',
                'question' => 'Има ли денонощни обекти като магазини, аптеки, заведения, бензиностанции?',
                'has_multiple_answers' => false,
                'answers' => [
                    ['title' => 'Да'],
                    ['title' => 'Не'],
                ],
            ],
            [
                'category' => 'пешеходна отсечка',
                'question' => 'Осветено ли е',
                'has_multiple_answers' => false,
                'answers' => [
                    ['title' => 'Да, достатъчно'],
                    ['title' => 'Да, но недостатъчно'],
                    ['title' => 'Не'],
                ],
            ],
            [
                'category' => 'пешеходна отсечка',
                'question' => 'Има ли изоставени сгради',
                'has_multiple_answers' => false,
                'answers' => [
                    ['title' => 'Да'],
                    ['title' => 'Не'],
                ],
            ],

        ];
    }
}
