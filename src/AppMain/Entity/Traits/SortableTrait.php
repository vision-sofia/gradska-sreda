<?php

namespace App\AppMain\Entity\Traits;

trait SortableTrait
{
    /**
     * @ORM\Column(name="position", type="smallint", nullable=true)
     */
    private $position = 1;

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
