<?php

namespace App\AppMain\Entity\Traits;

trait UUIDableTrait
{
    /**
     * @ORM\Column(type="guid", unique=true)
     */
    private $uuid;

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }
}
