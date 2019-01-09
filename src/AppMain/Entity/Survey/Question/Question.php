<?php

namespace App\AppMain\Entity\Survey\Question;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="q_question", schema="x_survey")
 * @ORM\Entity()
 */
class Question implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer", nullable=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Survey\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\AppMain\Entity\Survey\Question\Answer", mappedBy="question")
     */
    private $answers;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasMultipleAnswers;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }


    public function getAnswers()
    {
        return $this->answers;
    }


    public function setAnswers($answers): void
    {
        $this->answers = $answers;
    }


    public function getHasMultipleAnswers()
    {
        return $this->hasMultipleAnswers;
    }


    public function setHasMultipleAnswers($hasMultipleAnswers): void
    {
        $this->hasMultipleAnswers = $hasMultipleAnswers;
    }


    public function getCategory()
    {
        return $this->category;
    }


    public function setCategory($category): void
    {
        $this->category = $category;
    }
}
