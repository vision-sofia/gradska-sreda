<?php

namespace App\DataFixtures;

use App\AppManage\Entity\Settings;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class SettingsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $styles = [
            'cat1' => [
                'color' => '#0099ff',
                'opacity' => 0.8,
                'weight' => 7,
            ],
            'cat2' => [
                'color' => '#33cc33',
                'opacity' => 0.8,
                'weight' => 7,
            ],
            'cat3' => [
                'color' => '#ff3300',
                'opacity' => 0.8,
                'weight' => 7,
            ],
            'poly' => [
                'stroke' => '#ff3300',
                'strokeWidth' => 5,
                'strokeOpacity' => 0.2,
                'fill' => '#ff00ff',
                'fillOpacity' => 0.5,
            ],
            'line_main' => [
                'color' => '#ff99ff',
                'opacity' => 0.6,
                'weight' => 5,
            ],
            'line_hover' => [
                'opacity' => 1,
            ],
            'point_default' => [
                'radius' => 8,
                'fillColor' => '#ff7800',
                'color' => '#000',
                'weight' => 1,
                'opacity' => 1,
                'fillOpacity' => 0.8,
            ],
            'point_hover' => [
                'fillColor' => '#ff00ff',
            ],
            'poly_main' => [
                'stroke' => '#ff99ff',
                'strokeWidth' => 1,
                'strokeOpacity' => 0.2,
                'fill' => '#ff00ff',
                'fillOpacity' => 0.05,
            ],
            'poly_hover' => [
                'fillOpacity' => 0.3,
            ],
            'on_dialog_line' => [
                'color' => '#00ffff',
                'opacity' => 0.5,
            ],
            'on_dialog_point' => [
                'fillColor' => '#00ffff',
                'opacity' => 0.5,
            ],
            'on_dialog_polygon' => [
                'fillColor' => '#00ffff',
                'opacity' => 0.5,
            ],
        ];

        $entity = new Settings();
        $entity->setKey('map_style');
        $entity->setValue(json_encode($styles, JSON_PRETTY_PRINT));
        $entity->setType('json');
        $entity->setDescription('Map objects style');

        $manager->persist($entity);
        $manager->flush();
    }
}
