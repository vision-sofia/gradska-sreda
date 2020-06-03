<?php

namespace App\AppMain\Entity\Survey\Response;

use App\AppMain\Entity\Survey;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(
 *     name="response_answer",
 *     schema="x_survey",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"question_id", "answer_id"})}
 * )
 * @ORM\Entity
 */
class Answer implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Response\Question", inversedBy="answers")
     * @ORM\JoinColumn(referencedColumnName="id", name="question_id", nullable=false, onDelete="CASCADE")
     */
    private $question;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Question\Answer")
     * @ORM\JoinColumn(referencedColumnName="id", name="answer_id", nullable=false)
     */
    private $answer;

    /**
     * @ORM\Column(type="text")
     */
    private string $explanation = '';

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\File(mimeTypes={ "image/jpeg" })
     */
    private $photo;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private bool $isCompleted = false;

    public function getId(): int
    {
        return $this->id;
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function setQuestion(Survey\Response\Question $question): void
    {
        $this->question = $question;
    }

    public function getAnswer(): Collection
    {
        return $this->answer;
    }

    public function setAnswer(Survey\Question\Answer $answer): void
    {
        $this->answer = $answer;
    }

    public function getExplanation(): string
    {
        return $this->explanation;
    }

    public function setExplanation(string $explanation): void
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
}
