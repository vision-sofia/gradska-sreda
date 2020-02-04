<?php

/**
 * Properties naming convention: underscore
 */

namespace App\AppMain\DTO;

class ResponseAnswerDTO
{
    protected $id;
    protected $explanation;
    protected $photo;
    protected $question_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getExplanation(): ?string
    {
        return $this->explanation;
    }

    public function setExplanation(?string $explanation): void
    {
        $this->explanation = $explanation;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo): void
    {
        $this->photo = $photo;
    }

    public function getQuestionId(): ?int
    {
        return $this->question_id;
    }

    public function setQuestionId(int $questionId): void
    {
        $this->question_id = $questionId;
    }


}
