<?php

namespace App\DataFixtures\Survey\Question;

use App\AppMain\Entity\SurveySystem\Question\Answer;
use App\AppMain\Entity\SurveySystem\Question\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadQuestionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->data() as $question) {
            $questionObject = new Question();
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
            }
        }
    }


    private function data(): array
    {
        return [
            [
                'question'             => 'Какъв е автомобилният трафик в момента?',
                'has_multiple_answers' => false,
                'answers'              => [
                    ['title' => 'Интензивен',],
                    ['title' => 'Умерен',],
                    ['title' => 'Спокоен',],
                ],
            ],

            [
                'question'             => 'Какъв вид е пресичането?',
                'has_multiple_answers' => true,
                'answers'              => [
                    ['title' => 'Светофар'],
                    ['title' => 'Пешеходна пътека'],
                    ['title' => 'Пешеходен подлез'],
                    ['title' => 'Пешеходен мост / надлез'],
                    ['title' => 'Нерегулирано (квартални улици)'],
                    ['title' => 'Несъществуващо(!)'],
                ],
            ],

            [
                'question'             => 'Осветено ли е?',
                'category'             => 'алея',
                'has_multiple_answers' => false,
                'answers'              => [
                    ['title' => 'Да, достатъчно'],
                    ['title' => 'Да, но недостатъчно'],
                    ['title' => 'Не'],
                ],
            ],

            /* 'Помислено ли е за инвалидни и детски колички, възрастни хора и др.?' => [
                 'has_multiple_answers' => false,
                 'answers'              => [
                     'Да' => [
                         'Скосени бордюри / повдигната пешеходна повърхност',
                         'Тактилни плочки',
                         'Звукова сигнализация',
                     ],
                     'Не, никакви',
                 ],
             ],*/
        ];
    }
}
