<?php

namespace App\DataFixtures\Survey;

use App\AppMain\Entity\Survey\Evaluation\Definition;
use App\AppMain\Entity\Survey\Question\Answer;
use App\AppMain\Entity\Survey\Question\Question;
use App\AppMain\Entity\Survey\Survey\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\AppMain\Entity\Survey\Evaluation\Subject;

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

                if(isset($answer['evaluation'])) {
                    foreach ($answer['evaluation'] as $item) {
                        $criterionSubject = $manager->getRepository(Subject\Criterion::class)->findOneBy([
                            'name' => $item['criterion'],
                            'category' => $category
                        ]);
                        $criterionDefinition = new Definition\Criterion();
                        $criterionDefinition->setValue($item['point']);
                        $criterionDefinition->setSubject($criterionSubject);
                        $criterionDefinition->setAnswer($answerObject);

                        $manager->persist($criterionDefinition);
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
                'category' => 'Пресичания',
                'question' => 'Какъв е автомобилният трафик в момента?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Интензивен',
                    ],
                    [
                        'title' => 'Умерен',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Достъпност и проходимост'
                            ],
                        ]
                    ],
                    [
                        'title' => 'Спокоен',
                        'evaluation' => [
                            [
                                'point' => 2,
                                'criterion' => 'Достъпност и проходимост'
                            ],
                        ]
                    ],
                ],
            ],
            [
                'category' => 'Пресичания',
                'question' => 'Какъв вид е пресичането?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Светофар',
                        'evaluation' => [
                            [
                                'point' => 2,
                                'criterion' => 'Достъпност и проходимост'
                            ],
                            [
                                'point' => 2,
                                'criterion' => 'Сигурност'
                            ]
                        ]
                    ],
                    [
                        'title' => 'Пешеходна пътека',
                        'evaluation' => [
                            [
                                'point' => 2,
                                'criterion' => 'Достъпност и проходимост'
                            ],
                            [
                                'point' => 2,
                                'criterion' => 'Сигурност'
                            ]
                        ]
                    ],
                    [
                        'title' => 'Пешеходен подлез',
                        'evaluation' => [
                            [
                                'point' => 2,
                                'criterion' => 'Достъпност и проходимост'
                            ],
                            [
                                'point' => 2,
                                'criterion' => 'Сигурност'
                            ]
                        ]
                    ],
                    [
                        'title' => 'Пешеходен мост / надлез',
                        'evaluation' => [
                            [
                                'point' => 2,
                                'criterion' => 'Достъпност и проходимост'
                            ],
                            [
                                'point' => 2,
                                'criterion' => 'Сигурност'
                            ]
                        ]
                    ],
                    [
                        'title' => 'Нерегулирано (квартални улици)'
                    ],
                    [
                        'title' => 'Несъществуващо(!)'
                    ],
                ],
            ],
            [
                'category' => 'Пресичания',
                'question' => 'Помислено ли е за лица с намалена подвижност, незрящи, детски колички и др?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да',
                        'evaluation' => [
                            [
                                'point' => 2,
                                'criterion' => 'Достъпност и проходимост'
                            ],
                        ],
                        'child' => [
                            [
                                'title' => 'Скосени бордюри / повдигната пешеходна повърхност'
                            ],
                            [
                                'title' => 'Тактилни (релефни) плочки'
                            ],
                            [
                                'title' => 'Звукова сигнализация'
                            ],
                        ]
                    ],
                    [
                        'title' => 'Не, никакви'
                    ],
                ],
            ],
            [
                'category' => 'Пресичания',
                'question' => 'Има ли нещо, което в момента да затруднява видимостта или пресичането? (да не се появява при отговор “нерегулирано” на въпрос 2)',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да',
                        'child' => [
                            [
                                'title' => 'Паркирани коли'
                            ],
                            [
                                'title' => 'Кофи за боклук'
                            ],
                            [
                                'title' => 'Друго',
                                'is_free_answer' => true
                            ],
                        ]
                    ],
                    [
                        'title' => 'Не',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Достъпност и проходимост'
                            ],  [
                                'point' => 1,
                                'criterion' => 'Сигурност'
                            ],
                        ],
                    ],
                ],
            ],
            [
                'category' => 'Пресичания',
                'question' => 'Осветено ли е добре пресичането вечерно време?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Сигурност'
                            ],
                        ],
                    ],
                    [
                        'title' => 'Не',
                    ],
                ],
            ],
            [
                'category' => 'Алеи',
                'question' => 'Има ли проблеми с настилката в момента?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Не, никакви',
                        'evaluation' => [
                            [
                                'point' => 2,
                                'criterion' => 'Достъпност и проходимост'
                            ],
                        ],
                    ],
                    [
                        'title' => 'Да',
                        'child' => [
                            [
                                'title' => 'Има много неравности'
                            ],
                            [
                                'title' => 'Липса на настилка'
                            ],
                            [
                                'title' => 'Хлъзгаво е'
                            ],
                            [
                                'title' => 'Има наводнени участъци'
                            ],
                            [
                                'title' => 'Друго',
                                'is_free_answer' => true
                            ],
                        ]
                    ],
                ],
            ],
            [
                'category' => 'Алеи',
                'question' => 'Осветено ли е?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да, достатъчно',
                        'evaluation' => [
                            [
                                'point' => 2,
                                'criterion' => 'Сигурност'
                            ],
                        ],
                    ],
                    [
                        'title' => 'Да, но недостатъчно',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Сигурност'
                            ],
                        ],
                    ],
                    [
                        'title' => 'Не',
                    ],
                ],
            ],
            [
                'category' => 'Алеи',
                'question' => 'Хората само преминават или има и стоящи/седящи хора наоколо в момента?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да, сами',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Комфорт и привлекателност'
                            ],
                        ],
                    ],
                    [
                        'title' => 'Да, в група от двама или повече',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Комфорт и привлекателност'
                            ],
                        ],
                    ],
                    [
                        'title' => 'Не',
                    ],
                ],
            ],
            [
                'category' => 'Алеи',
                'question' => 'Има ли озеленяване?  (дървета, храсти, тревни площи и др.)?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да, в добро състояние',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Комфорт и привлекателност'
                            ],
                        ],
                    ],
                    [
                        'title' => 'Да, в лошо състояние',
                        'evaluation' => [
                            [
                                'point' => 0.5,
                                'criterion' => 'Комфорт и привлекателност'
                            ],
                        ],
                    ],
                    [
                        'title' => 'Не, няма',
                    ],
                ],
            ],
            [
                'category' => 'Алеи',
                'question' => 'Има ли градско обзавеждане (пейки, кошчета и др.)?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да, в добро състояние',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Комфорт и привлекателност'
                            ],
                        ],
                    ],
                    [
                        'title' => 'Да, в лошо състояние',
                        'evaluation' => [
                            [
                                'point' => 0.5,
                                'criterion' => 'Комфорт и привлекателност'
                            ],
                        ],
                    ],
                    [
                        'title' => 'Не, няма',
                    ],
                ],
            ],
        ];
    }
}
