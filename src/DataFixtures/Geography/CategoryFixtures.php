<?php

namespace App\DataFixtures\Geography;

use App\AppMain\Entity\SurveySystem\Evaluation\Context;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager):void
    {
        /*
        foreach ($this->data() as $item) {
            $object = new Context();
            $object->setName($item);

            $manager->persist($object);
        }

        $manager->flush();
        */
    }

    private function data():array {
        return [
            'Пресичания',
            'Пешеходни отсечки',
        ];
    }
}
