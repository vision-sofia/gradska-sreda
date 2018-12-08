<?php

namespace App\DataFixtures\Poll\Poll;


use App\AppMain\Entity\SurveySystem\Survey\Survey;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPollFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->data() as $item) {
            $object = new Survey();
            $object->setName($item);

            $manager->persist($object);
        }

        $manager->flush();
    }


    private function data(): array
    {
        return [
            'Анкета',
        ];
    }
}
