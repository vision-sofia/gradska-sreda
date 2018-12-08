<?php

namespace App\DataFixtures\Poll\Poll;


use App\DataFixtures\Poll\Question\LoadQuestionFixtures;
use App\AppMain\Entity\SurveySystem\Survey\Survey;
use App\AppMain\Entity\SurveySystem\Survey\Subject;
use App\AppMain\Entity\SurveySystem\Question\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPollSubjectFixture extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            LoadPollFixtures::class,
            LoadQuestionFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $survey = $manager->getRepository(Survey::class)->findOneBy([]);

        $questions = $manager->getRepository(Question::class)->findAll();

        if ($survey && count($questions) > 0) {
            foreach ($questions as $question) {
                $subject = new Subject();
                $subject->setSurvey($survey);
                $subject->setQuestion($question);

                $manager->persist($subject);
            }

            $manager->flush();
        }
    }

}
