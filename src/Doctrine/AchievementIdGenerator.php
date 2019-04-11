<?php


namespace App\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

class AchievementIdGenerator extends AbstractIdGenerator
{
    public function generate(EntityManager $em, $entity)
    {
        return 'nextval(\'x_main.a_achievement_id_seq\')';
    }
}
