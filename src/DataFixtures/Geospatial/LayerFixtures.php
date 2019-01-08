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
            $layer = new Layer();
            $layer->setName($item);

            $manager->persist($layer);
            $manager->flush();
        }
    }

    private function data():array {
        return [
            'тротоар',
            'алея',
            'пресичане',
        ];
    }
}
