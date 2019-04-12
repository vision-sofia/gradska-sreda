<?php

namespace App\AppMain\Entity\Achievement;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="a_result", schema="x_main")
 * @ORM\Entity()
 */
class UserResult extends AbstractAchievement
{
    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\User\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
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

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getAchievement()
    {
        return $this->achievement;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count): void
    {
        $this->count = $count;
    }

    /**
     * @return mixed
     */
    public function getIsCompleted()
    {
        return $this->isCompleted;
    }

    /**
     * @param mixed $isCompleted
     */
    public function setIsCompleted($isCompleted): void
    {
        $this->isCompleted = $isCompleted;
    }


}
