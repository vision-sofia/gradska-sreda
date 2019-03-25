<?php
/**
 * Properties naming convention: underscore
 */

namespace App\AppMain\DTO;

class QuestionAnswerDTO
{
    protected $id;
    protected $uuid;
    protected $title;
    protected $parent;
    protected $is_free_answer;
    protected $is_photo_enabled;
    protected $explanation;
    protected $photo;
    protected $is_selected = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getParent(): ?int
    {
        return $this->parent;
    }

    public function setParent(?int $parent): void
    {
        $this->parent = $parent;
    }

    public function getIsFreeAnswer(): ?bool
    {
        return $this->is_free_answer;
    }

    public function setIsFreeAnswer(bool $isFreeAnswer): void
    {
        $this->is_free_answer = $isFreeAnswer;
    }

    public function getIsPhotoEnabled(): ?bool
    {
        return $this->is_photo_enabled;
    }

    public function setIsPhotoEnabled(bool $isPhotoEnabled): void
    {
        $this->is_photo_enabled = $isPhotoEnabled;
    }

    public function getExplanation()
    {
        return $this->explanation;
    }

    public function setExplanation($explanation): void
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

    public function getIsSelected():bool
    {
        return $this->is_selected;
    }

    public function setIsSelected($isSelected): void
    {
        $this->is_selected = $isSelected;
    }

    public static function fromStd(object $object): QuestionAnswerDTO
    {
        $instance = new self();

        if (isset($object->id)) {
            $instance->setId($object->id);
        }

        if (isset($object->uuid)) {
            $instance->setUuid($object->uuid);
        }

        if (isset($object->parent)) {
            $instance->setParent($object->parent);
        }

        if (isset($object->title)) {
            $instance->setTitle($object->title);
        }

        if (isset($object->is_free_answer)) {
            $instance->setIsFreeAnswer($object->is_free_answer);
        }

        if (isset($object->is_photo_enabled)) {
            $instance->setIsPhotoEnabled($object->is_photo_enabled);
        }

        return $instance;
    }
}
