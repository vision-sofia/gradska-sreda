<?php

namespace App\DataFixtures\Survey;


use App\AppMain\Entity\Survey\Survey\Survey;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class SurveyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->data() as $item) {
            $object = new Survey();
            $object->setName($item['name']);
            $object->setIsActive($item['is_active']);
            $object->setStartDate(new \DateTime($item['start_date']));
            $object->setEndDate(new \DateTime($item['end_date']));

            $manager->persist($object);
        }

        $manager->flush();
    }


    private function data(): array
    {
        return [
            [
                'name' => 'Анкета',
                'is_active' => true,
                'start_date' => '2019-01-01',
                'end_date' => '2019-06-01',
            ]
        ];
    }
}
