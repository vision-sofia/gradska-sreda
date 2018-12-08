<?php

namespace App\AppMain\Entity;

interface UuidInterface
{
    public function getUuid(): ?string;

    public function setUuid(string $uuid): void;
}
