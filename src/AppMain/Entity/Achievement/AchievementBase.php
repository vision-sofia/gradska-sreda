<?php


namespace App\AppMain\Entity\Achievement;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="a_achievement", schema="x_main")
 * @ORM\Entity()
 */
class Achievement extends AbstractAchievement
{

}
