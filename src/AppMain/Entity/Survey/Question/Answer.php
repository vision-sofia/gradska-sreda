<?php

namespace App\AppMain\Entity\Survey\Question;

use App\AppMain\Entity\Traits\SortableTrait;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="q_answer", schema="x_survey")
 * @ORM\Entity(repositoryClass="App\AppMain\Repository\Survey\Question\AnswerRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Question\Question", inversedBy="answers")
     */
    private $question;

    /**
     * @ORM\OneToMany(targetEntity="App\AppMain\Entity\Survey\Question\Answer", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Question\Answer", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=true)
     */
    private $parent;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isFreeAnswer;


    public function __construct()
    {
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

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): void
    {
        $this->question = $question;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function setChildren($children): void
    {
        $this->children = $children;
    }

    public function getParent(): ?Answer
    {
        return $this->parent;
    }

    public function setParent(?Answer $parent): void
    {
        $this->parent = $parent;
    }

    public function getIsFreeAnswer(): bool
    {
        return $this->isFreeAnswer;
    }

    public function setIsFreeAnswer(bool $isFreeAnswer): void
    {
        $this->isFreeAnswer = $isFreeAnswer;
    }
}
