<?php

namespace App\DataFixtures\Survey;

use App\AppMain\Entity\Survey\Evaluation\Definition;
use App\AppMain\Entity\Survey\Evaluation\Subject;
use App\AppMain\Entity\Survey\Question\Answer;
use App\AppMain\Entity\Survey\Question\Question;
use App\AppMain\Entity\Survey\Survey\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class QuestionFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->data() as $question) {
            $category = $manager->getRepository(Category::class)
                ->findOneBy([
                    'name' => $question['category'],
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
                $answerObject->setIsChildAnswerRequired($answer['is_child_answer_required'] ?? false);
                $answerObject->setIsFreeAnswer(false);
                $answerObject->setIsPhotoEnabled($answer['is_photo_enabled'] ?? false);

                $manager->persist($answerObject);
                $manager->flush();

                if (isset($answer['child'])) {
                    foreach ($answer['child'] as $item) {
                        $answerChild = new Answer();
                        $answerChild->setTitle($item['title']);
                        $answerChild->setQuestion($questionObject);
                        $answerChild->setParent($answerObject);
                        $answerChild->setIsFreeAnswer(isset($item['is_free_answer']) && true === $item['is_free_answer']);
                        $answerChild->setIsPhotoEnabled($answer['is_photo_enabled'] ?? false);

                        $manager->persist($answerChild);
                        $manager->flush();
                    }
                }

                if (isset($answer['evaluation'])) {
                    foreach ($answer['evaluation'] as $item) {
                        $criterionSubject = $manager->getRepository(Subject\Criterion::class)->findOneBy([
                            'name' => $item['criterion'],
                            'category' => $category,
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
                                'criterion' => 'Достъпност и проходимост',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Спокоен',
                        'evaluation' => [
                            [
                                'point' => 2,
                                'criterion' => 'Достъпност и проходимост',
                            ],
                        ],
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
                                'criterion' => 'Достъпност и проходимост',
                            ],
                            [
                                'point' => 2,
                                'criterion' => 'Сигурност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Пешеходна пътека',
                        'evaluation' => [
                            [
                                'point' => 2,
                                'criterion' => 'Достъпност и проходимост',
                            ],
                            [
                                'point' => 2,
                                'criterion' => 'Сигурност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Пешеходен подлез',
                        'evaluation' => [
                            [
                                'point' => 2,
                                'criterion' => 'Достъпност и проходимост',
                            ],
                            [
                                'point' => 2,
                                'criterion' => 'Сигурност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Пешеходен мост / надлез',
                        'evaluation' => [
                            [
                                'point' => 2,
                                'criterion' => 'Достъпност и проходимост',
                            ],
                            [
                                'point' => 2,
                                'criterion' => 'Сигурност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Нерегулирано (квартални улици)',
                    ],
                    [
                        'title' => 'Несъществуващо(!)',
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
                                'criterion' => 'Достъпност и проходимост',
                            ],
                        ],
                        'is_child_answer_required' => true,
                        'child' => [
                            [
                                'title' => 'Скосени бордюри / повдигната пешеходна повърхност',
                            ], [
                                'title' => 'Тактилни (релефни) плочки',
                            ], [
                                'title' => 'Звукова сигнализация',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Не, никакви',
                    ],
                ],
            ],
            [
                'category' => 'Пресичания',
                'question' => 'Има ли нещо, което в момента да затруднява видимостта или пресичането?', // TODO: да не се появява при отговор “нерегулирано” на въпрос 2
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да',
                        'is_photo_enabled' => true,
                        'is_child_answer_required' => true,
                        'child' => [
                            [
                                'title' => 'Паркирани коли',
                            ],
                            [
                                'title' => 'Кофи за боклук',
                            ],
                            [
                                'title' => 'Друго',
                                'is_free_answer' => true,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Не',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Достъпност и проходимост',
                            ], [
                                'point' => 1,
                                'criterion' => 'Сигурност',
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
                                'criterion' => 'Сигурност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Не',
                    ],
                ],
            ],
            [
                'category' => 'Пешеходни отсечки',
                'question' => 'Има ли някакви препятствия в тази отсечка?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да, по цялото продължение на отсечката',
                        'is_photo_enabled' => true,
                        'is_child_answer_required' => true,
                        'child' => [
                            [
                                'title' => 'Паркирани коли',
                            ],
                            [
                                'title' => 'Кофи за боклук',
                            ],
                            [
                                'title' => 'Маси на заведения',
                            ],
                            [
                                'title' => 'Спирки на МГТ',
                            ],
                            [
                                'title' => 'Несъобразено поставени осветителни стълбове и реклами',
                            ],
                            [
                                'title' => 'Антипаркинг колчета',
                            ],
                            [
                                'title' => 'Друго',
                                'is_free_answer' => true,
                            ],
                        ],
                    ],
                    [
                        'title' => 'Да, епизодично',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Достъпност и проходимост',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Не, никакви',
                        'evaluation' => [
                            [
                                'point' => 2,
                                'criterion' => 'Достъпност и проходимост',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'category' => 'Пешеходни отсечки',
                'question' => 'Има ли сериозен конфликт с велосипедисти, товарни дейности, скутери, скейтборд или други?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да',
                        'is_photo_enabled' => true,
                    ],
                    [
                        'title' => 'Не',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Достъпност и проходимост',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'category' => 'Пешеходни отсечки',
                'question' => 'Има ли проблеми с настилката в момента?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Не, никакви',
                        'evaluation' => [
                            [
                                'point' => 2,
                                'criterion' => 'Достъпност и проходимост',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Да',
                        'is_photo_enabled' => true,
                        'is_child_answer_required' => true,
                        'child' => [
                            [
                                'title' => 'Има много неравности',
                            ],
                            [
                                'title' => 'Липса на настилка',
                            ],
                            [
                                'title' => 'Хлъзгаво е',
                            ],
                            [
                                'title' => 'Има наводнени участъци',
                            ],
                            [
                                'title' => 'Друго',
                                'is_free_answer' => true,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'category' => 'Пешеходни отсечки',
                'question' => 'Има ли ‘светли’ и активни партерни етажи (наличие на търговски обекти)?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Сигурност',
                            ],
                            [
                                'point' => 0.5,
                                'criterion' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Не',
                    ],
                ],
            ],
            [
                'category' => 'Пешеходни отсечки',
                'question' => 'Има ли денонощни обекти като магазини, аптеки, заведения, бензиностанции?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да',
                        'evaluation' => [
                            [
                                'point' => 0.5,
                                'criterion' => 'Сигурност',
                            ],
                            [
                                'point' => 0.5,
                                'criterion' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Не',
                    ],
                ],
            ],
            [
                'category' => 'Пешеходни отсечки',
                'question' => 'Осветено ли е?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да, достатъчно',
                        'evaluation' => [
                            [
                                'point' => 2,
                                'criterion' => 'Сигурност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Да, но недостатъчно',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Сигурност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Не',
                    ],
                ],
            ],
            [
                'category' => 'Пешеходни отсечки',
                'question' => 'Има ли изоставени сгради?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да',
                        'is_photo_enabled' => true,
                    ],
                    [
                        'title' => 'Не',
                        'evaluation' => [
                            [
                                'point' => 0.5,
                                'criterion' => 'Сигурност',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'category' => 'Пешеходни отсечки',
                'question' => 'Има ли хора в тази зона?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да, има седящи/стоящи',
                        'evaluation' => [
                            [
                                'point' => 0.5,
                                'criterion' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Да, има преминаващи',
                        'evaluation' => [
                            [
                                'point' => 0.5,
                                'criterion' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Няма',
                    ],
                ],
            ],
            [
                'category' => 'Пешеходни отсечки',
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
                                'point' => 0.5,
                                'criterion' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Спокоен',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'category' => 'Пешеходни отсечки',
                'question' => 'Има ли озеленяване?  (дървета, храсти, тревни площи и др.)',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да, в добро състояние',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Да, в лошо състояние',
                        'is_photo_enabled' => true,
                        'evaluation' => [
                            [
                                'point' => 0.5,
                                'criterion' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Не, няма',
                    ],
                ],
            ],
            [
                'category' => 'Пешеходни отсечки',
                'question' => 'Има ли градско обзавеждане (пейки, кошчета и др.)?',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Да, в добро състояние',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Да, в лошо състояние',
                        'is_photo_enabled' => true,
                        'evaluation' => [
                            [
                                'point' => 0.5,
                                'criterion' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Не, няма',
                    ],
                ],
            ],
            [
                'category' => 'Пешеходни отсечки',
                'question' => 'Има ли неподдържани фасади? (ронеща се мазилка и др.)',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Много',
                        'is_photo_enabled' => true,
                        'is_free_answer' => true,
                    ],
                    [
                        'title' => 'Малко',
                        'is_photo_enabled' => true,
                        'evaluation' => [
                            [
                                'point' => 0.5,
                                'criterion' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Не, няма',
                        'evaluation' => [
                            [
                                'point' => 0.5,
                                'criterion' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Няма сгради',
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
                                'criterion' => 'Достъпност и проходимост',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Да',
                        'is_photo_enabled' => true,
                        'is_child_answer_required' => true,
                        'child' => [
                            [
                                'title' => 'Има много неравности',
                            ],
                            [
                                'title' => 'Липса на настилка',
                            ],
                            [
                                'title' => 'Хлъзгаво е',
                            ],
                            [
                                'title' => 'Има наводнени участъци',
                            ],
                            [
                                'title' => 'Друго',
                                'is_free_answer' => true,
                            ],
                        ],
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
                                'criterion' => 'Сигурност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Да, но недостатъчно',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Сигурност',
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
                                'criterion' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Да, в група от двама или повече',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Комфорт и привлекателност',
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
                                'criterion' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Да, в лошо състояние',
                        'is_photo_enabled' => true,
                        'evaluation' => [
                            [
                                'point' => 0.5,
                                'criterion' => 'Комфорт и привлекателност',
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
                                'criterion' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Да, в лошо състояние',
                        'is_photo_enabled' => true,
                        'evaluation' => [
                            [
                                'point' => 0.5,
                                'criterion' => 'Комфорт и привлекателност',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Не, няма',
                    ],
                ],
            ],
            [
                'category' => 'Категория-1',
                'question' => 'Въпрос-1',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Отг.1',
                        'is_photo_enabled' => true,
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Критерии-1',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Отг.2',
                    ],
                ],
            ],
            [
                'category' => 'Категория-1',
                'question' => 'Въпрос-2',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Отг.1',
                    ],
                    [
                        'title' => 'Отг.2',
                        'is_photo_enabled' => true,
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Критерии-2',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'category' => 'Категория-1',
                'question' => 'Въпрос-3',
                'has_multiple_answers' => false,
                'answers' => [
                    [
                        'title' => 'Отг.1',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Критерии-1',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Отг.2',
                        'evaluation' => [
                            [
                                'point' => 1,
                                'criterion' => 'Критерии-2',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
