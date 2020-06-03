<?php

namespace App\AppMain\Entity\Survey\Question;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="q_flow", schema="x_survey")
 * @ORM\Entity
 */
class Flow implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Question\Question")
     */
    private ?Question $question = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Question\Answer")
     */
    private ?Answer $answer = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $action = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): void
    {
        $this->question = $question;
    }

    public function getAnswer(): Answer
    {
        return $this->answer;
    }

    public function setAnswer(Answer $answer): void
    {
        $this->answer = $answer;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }
}
