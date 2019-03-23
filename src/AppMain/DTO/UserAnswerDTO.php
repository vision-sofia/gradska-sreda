<?php

namespace App\AppMain\DTO;

class UserAnswerDTO
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id): void
    {
        $this->id = $id;
    }
}
