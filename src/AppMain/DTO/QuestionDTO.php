<?php

namespace App\AppMain\DTO;

class QuestionDTO
{
    protected $id;
    protected $answers;

    public function getAnswers()
    {
        return $this->answers;
    }

    public function setAnswers($answers): void
    {
        $this->answers = $answers;
    }
}
