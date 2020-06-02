<?php

namespace App\DataFixtures\Survey;

use App\AppMain\Entity\Survey\Question\Answer;
use App\AppMain\Entity\Survey\Question\Flow;
use App\AppMain\Entity\Survey\Question\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FlowFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            QuestionFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->data() as $value) {
            $question = $manager->getRepository(Question::class)
                                ->findOneBy([
                                    'title' => $value['question'],
                                ])
            ;

            foreach ($value['answers'] as $answer) {
                $answer = $manager->getRepository(Answer::class)->findOneBy([
                    'title' => $answer,
                ])
                ;

                if ($question) {
                    $flow = new Flow();
                    $flow->setQuestion($question);
                    $flow->setAnswer($answer);
                    $flow->setAction('skip');
                    $manager->persist($flow);
                }
            }

            $manager->flush();
        }
    }

    private function data(): array
    {
        return [
            [
                'answers' => [
                    'Спокоен',
                ],
                'question' => 'Какъв вид е пресичането?',
            ],
        ];
    }
}
