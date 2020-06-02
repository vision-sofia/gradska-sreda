<?php

namespace App\DataFixtures;

use App\AppMain\Entity\Achievement\CategoryCompletionAchievement;
use App\AppMain\Entity\Survey\Survey\Category;
use App\DataFixtures\Survey\CategoryFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AchievementFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        /** @var Category[] $categories */
        $categories = $manager->getRepository(Category::class)
            ->findAll()
        ;

        $c = [];

        foreach ($categories as $item) {
            $c[$item->getName()] = $item;
        }

        foreach ($this->data() as $data) {
            $achievement = new CategoryCompletionAchievement();
            $achievement->setTitle($data['title']);
            $achievement->setDescription($data['desc']);
            $achievement->setThreshold($data['threshold']);
            $achievement->setSurveyCategory($c[$data['category']]);

            //$manager->persist($achievement);
        }

        //$manager->flush();
    }

    private function data(): array
    {
        return [
            [
                'title' => 'Попълни анкета за едно пресичане',
                'desc' => 'Попълни анкета за едно пресичане',
                'threshold' => 1,
                'category' => 'Пресичания',
            ], [
                'title' => 'Попълни анкета за една пешеходна отсечка',
                'desc' => 'Попълни анкета за едно пешеходна отсечки',
                'threshold' => 1,
                'category' => 'Пешеходни отсечки',
            ], [
                'title' => 'Попълни анкета за една алея',
                'desc' => 'Попълни анкета за една алея',
                'threshold' => 1,
                'category' => 'Алеи',
            ], [
                'title' => 'Попълни анкета за 10 пресичания',
                'desc' => 'Попълни анкета за 10 пресичания',
                'threshold' => 10,
                'category' => 'Пресичания',
            ], [
                'title' => 'Попълни анкета за 10 пешеходни отсечки',
                'desc' => 'Попълни анкета за 10 пешеходни отсечки',
                'threshold' => 10,
                'category' => 'Пешеходни отсечки',
            ], [
                'title' => 'Попълни анкета за 10 алеи',
                'desc' => 'Попълни анкета за 10 алеи',
                'threshold' => 10,
                'category' => 'Алеи',
            ],
        ];
    }
}
