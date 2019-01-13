<?php

namespace App\DataFixtures\Survey;

use App\AppMain\Entity\Geospatial\Layer;
use App\AppMain\Entity\Survey\Evaluation;
use App\AppMain\Entity\Survey\Evaluation\Subject;
use App\AppMain\Entity\Survey\Question\Answer;
use App\AppMain\Entity\Survey\Question\Question;
use App\AppMain\Entity\Survey\Survey\Category;
use App\AppMain\Entity\Survey\Survey\Flow;
use App\AppMain\Entity\Survey\Survey\Survey;
use App\DataFixtures\Geospatial\LayerFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

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
                                        'title' => $value['question']
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
                'answers'  => [
                    'Умерен',
                    'Спокоен',
                ],
                'question' => 'Какъв вид е пресичането?',
            ],
        ];
    }
}
