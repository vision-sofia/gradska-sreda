<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SettingsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /*
        $entity = new Settings();
        $entity->setKey('x');
        $entity->setValue(json_encode([], JSON_PRETTY_PRINT));
        $entity->setType('json');
        $entity->setDescription('');

        $manager->persist($entity);
        $manager->flush();
        */
    }
}
