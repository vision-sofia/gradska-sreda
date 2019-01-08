<?php

namespace App\AppMain\Entity\SurveySystem\Question;

use App\AppMain\Entity\Traits\SortableTrait;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="question_answer", schema="x_survey")
 * @ORM\Entity()
 */
class Answer implements UuidInterface
{
    use UUIDableTrait;
    use SortableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\SurveySystem\Question\Question", inversedBy="answers")
     */
    private $question;

    /**
     * @ORM\OneToMany(targetEntity="App\AppMain\Entity\SurveySystem\Question\Answer", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\SurveySystem\Question\Answer", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=true)
     */
    private $parent;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isFreeAnswer;

    public function __construct() {
        $this->children = new ArrayCollection();
    }

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

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question): void
    {
        $this->question = $question;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children): void
    {
        $this->children = $children;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getIsFreeAnswer()
    {
        return $this->isFreeAnswer;
    }

    /**
     * @param mixed $isFreeAnswer
     */
    public function setIsFreeAnswer($isFreeAnswer): void
    {
        $this->isFreeAnswer = $isFreeAnswer;
    }


}
