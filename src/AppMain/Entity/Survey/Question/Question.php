<?php

namespace App\AppMain\Entity\Survey\Question;

use App\AppMain\Entity\Survey\Survey\Category;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="q_question", schema="x_survey")
 * @ORM\Entity(repositoryClass="App\AppMain\Repository\Survey\Question\QuestionRepository")
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
    private $title = '';

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

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): void
    {
        $answer->setQuestion($this);

        $this->answers[] = $answer;
    }

    public function getHasMultipleAnswers(): bool
    {
        return $this->hasMultipleAnswers;
    }

    public function setHasMultipleAnswers(bool $hasMultipleAnswers): void
    {
        $this->hasMultipleAnswers = $hasMultipleAnswers;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }
}
