<?php

namespace App\DataFixtures;

use App\AppMain\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $entity = new User();
        $entity->setEmail('admin@localhost');
        $entity->setIsActive(true);
        $entity->setUsername('admin');
        $entity->setPassword($this->encoder->encodePassword($entity, 'demo'));
        $entity->addRole('ROLE_ADMIN');

        $manager->persist($entity);

        $entity = new User();
        $entity->setEmail('test@localhost');
        $entity->setIsActive(true);
        $entity->setUsername('test');
        $entity->setPassword($this->encoder->encodePassword($entity, 'demo'));
        $entity->addRole('ROLE_MANAGE');

        $manager->persist($entity);

        $entity = new User();
        $entity->setEmail('foo@localhost');
        $entity->setIsActive(true);
        $entity->setUsername('foo');
        $entity->setPassword($this->encoder->encodePassword($entity, 'demo'));
        $entity->addRole('ROLE_USER');

        $manager->persist($entity);
        $manager->flush();
    }
}
