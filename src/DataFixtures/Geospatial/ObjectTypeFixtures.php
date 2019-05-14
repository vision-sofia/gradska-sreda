<?php

namespace App\DataFixtures\Geospatial;

use App\AppMain\Entity\Geospatial\ObjectType;
use App\AppMain\Entity\Geospatial\ObjectTypeVisibility;
use App\Doctrine\ValueObject\IntRange;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ObjectTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->data() as $item) {
            $objectType = new ObjectType();
            $objectType->setName($item['name']);

            $manager->persist($objectType);
            $manager->flush();

            $objectTypeVisibility = new ObjectTypeVisibility();
            $objectTypeVisibility->setObjectType($objectType);
            $objectTypeVisibility->setZoom(new IntRange($item['max_zoom'], $item['min_zoom']));

            $manager->persist($objectTypeVisibility);
            $manager->flush();
        }
    }

    private function data(): array
    {
        return [
            [
                'name' => 'Пътно платно',
                'min_zoom' => 22,
                'max_zoom' => 16,
            ], [
                'name' => 'Нерегулирано',
                'min_zoom' => 22,
                'max_zoom' => 16,
            ], [
                'name' => 'Тротоар',
                'min_zoom' => 22,
                'max_zoom' => 16,
            ], [
                'name' => 'Паркинг',
                'min_zoom' => 22,
                'max_zoom' => 16,
            ], [
                'name' => 'Пешеходна пътека',
                'min_zoom' => 22,
                'max_zoom' => 16,
            ], [
                'name' => 'Светофар',
                'min_zoom' => 22,
                'max_zoom' => 16,
            ], [
                'name' => 'Алея с настилка',
                'min_zoom' => 22,
                'max_zoom' => 16,
            ], [
                'name' => 'Алея без настилка',
                'min_zoom' => 22,
                'max_zoom' => 16,
            ], [
                'name' => 'Алея',
                'min_zoom' => 22,
                'max_zoom' => 16,
            ], [
                'name' => 'Подлез',
                'min_zoom' => 22,
                'max_zoom' => 16,
            ], [
                'name' => 'Стълбище',
                'min_zoom' => 22,
                'max_zoom' => 16,
            ], [
                'name' => 'Спирка на градски транспорт',
                'min_zoom' => 22,
                'max_zoom' => 14,
            ], [
                'name' => 'Спирка на метро',
                'min_zoom' => 22,
                'max_zoom' => 5,
            ], [
                'name' => 'Градоустройствена единица',
                'min_zoom' => 17,
                'max_zoom' => 14,
            ], [
                'name' => 'Административен райони',
                'min_zoom' => 15,
                'max_zoom' => 4,
            ], [
                'name' => 'Строителна граница',
                'min_zoom' => 17,
                'max_zoom' => 4,
            ],
        ];
    }
}
