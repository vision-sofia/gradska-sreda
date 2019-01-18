<?php

namespace App\DataFixtures\Geospatial;

use App\AppMain\Entity\Geospatial\ObjectType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ObjectTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager):void
    {
        foreach ($this->data() as $item) {
            $layer = new ObjectType();
            $layer->setName($item);

            $manager->persist($layer);
            $manager->flush();
        }
    }

    private function data():array {
        return [
            'Пътно платно',
            'Нерегулирано',
            'Тротоар',
            'Паркинг',
            'Пешеходна пътека',
            'Светофар',
            'Алея с настилка',
            'Алея без настилка',
            'Алея',
            'Подлез',
            'Стълбище',
        ];
    }
}
