<?php

namespace App\AppMain\Entity\User;

use App\AppMain\Entity\UuidInterface;

interface UserInterface extends UuidInterface
{
    public function getId(): int;

    public function getUsername(): ?string;

    public function setUsername(string $username);
}
