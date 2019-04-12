<?php

namespace App\AppMain\Entity\Achievement;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="a_result", schema="x_main")
 * @ORM\Entity()
 */
class UserResult
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\User\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Achievement\AchievementBase")
     * @ORM\JoinColumn(nullable=false)
     */
    private $achievement;

    /**
     * @ORM\Column(type="smallint")
     */
    private $count;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCompleted;

    public function getUser()
    {
        return $this->user;
    }

    public function getAchievement()
    {
        return $this->achievement;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function getIsCompleted(): ?bool
    {
        return $this->isCompleted;
    }
}
