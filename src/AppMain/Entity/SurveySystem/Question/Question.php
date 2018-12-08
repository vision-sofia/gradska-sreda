<?php

namespace App\AppMain\Entity\SurveySystem\Question;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="question", schema="x_survey")
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
     * @ORM\OneToMany(targetEntity="App\AppMain\Entity\SurveySystem\Question\Answer", mappedBy="question")
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


}
