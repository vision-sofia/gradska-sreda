<?php
/**
 * Properties naming convention: underscore
 */

namespace App\AppMain\DTO;

class QuestionDTO
{
    protected $id;
    protected $uuid;
    protected $title;
    protected $has_multiple_answers;
    protected $answers;
    protected $is_answered = false;

    public function getAnswers()
    {
        return $this->answers;
    }

    public function setAnswers($answers): void
    {
        $this->answers = $answers;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid($uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function getHasMultipleAnswers()
    {
        return $this->has_multiple_answers;
    }

    public function setHasMultipleAnswers($hasMultipleAnswers): void
    {
        $this->has_multiple_answers = $hasMultipleAnswers;
    }

    public function isAnswered(): bool
    {
        return $this->is_answered;
    }

    public function setIsAnswered(bool $isAnswered): void
    {
        $this->is_answered = $isAnswered;
    }
}
