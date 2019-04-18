<?php

namespace App\DataFixtures;

use App\AppMain\Entity\Achievement\CategoryCompletionAchievement;
use App\AppMain\Entity\StaticPage;
use App\AppMain\Entity\Survey\Survey\Category;
use App\DataFixtures\Survey\CategoryFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class StaticPageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->data() as $data) {
            $staticPage = new StaticPage();
            $staticPage->setSlug($data['slug']);
            $staticPage->setTitle($data['title']);
            $staticPage->setContent($data['content']);

            $manager->persist($staticPage);
        }

        $manager->flush();
    }

    private function data(): array
    {
        return [
            [
                'slug' => 'privacy-policy',
                'title' => 'Политика за лични данни',
                'content' => '# Политика за лични данни',
            ],
            [
                'slug' => 'terms-and-conditions',
                'title' => 'Условия за ползване',
                'content' => '# Условия за ползване',
            ],
            [
                'slug' => 'about',
                'title' => 'За платформата',
                'content' => '# За платформата',
            ],
        ];
    }
}
