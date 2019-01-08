<?php

namespace App\DataFixtures;

use App\AppMain\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $entity = new User();
        $entity->setEmail('admin@localhost.com');
        $entity->setIsActive(true);
        $entity->setUsername('admin');
        $entity->setPassword('$argon2i$v=19$m=1024,t=1,p=1$c29tZXNhbHQ$BjWlpk8/CC9Ei/G14zrVbgwBLK8Nq1e9bk2Bk1LOqGc'); // 123@

        $entity->addRole('ROLE_ADMIN');

        $manager->persist($entity);
        $manager->flush();
    }
}
