<?php

namespace App\DataFixtures\Geospatial;

use App\AppMain\Entity\Geospatial\Layer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LayerFixtures extends Fixture
{
    public function load(ObjectManager $manager):void
    {
        foreach ($this->data() as $item) {
            $object = new Layer();
            $object->setName($item);

            $manager->persist($object);
        }

        $manager->flush();
    }

    private function data():array {
        return [
            'Тротоар',
            'Алея',
            'Пресичане',
        ];
    }
}
