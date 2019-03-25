<?php

namespace App\AppMain\DTO;

class QuestionDTO
{
    protected $id;
    protected $uuid;
    protected $title;
    protected $has_multiple_answers;
    protected $answers;

    public function getAnswers()
    {
        return $this->answers;
    }

    public function setAnswers($answers): void
    {
        $this->answers = $answers;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param mixed $uuid
     */
    public function setUuid($uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getHasMultipleAnswers()
    {
        return $this->has_multiple_answers;
    }

    /**
     * @param mixed $has_multiple_answers
     */
    public function setHasMultipleAnswers($has_multiple_answers): void
    {
        $this->has_multiple_answers = $has_multiple_answers;
    }


}
