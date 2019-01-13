<?php

namespace App\AppMain\Entity\Survey\Survey;

use App\AppMain\Entity\Survey\EvaluationDefinition\Indicator;
use App\AppMain\Entity\Survey\Question\Answer;
use App\AppMain\Entity\Survey\Question\Question;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="flow", schema="x_survey")
 * @ORM\Entity()
 */
class Flow implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Question\Question")
     */
    private $question;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Question\Answer")
     */
    private $answer;

    public function getId(): int
    {
        return $this->id;
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function setQuestion(Question $question): void
    {
        $this->question = $question;
    }

    public function getAnswer()
    {
        return $this->answer;
    }

    public function setAnswer(Answer $answer): void
    {
        $this->answer = $answer;
    }
}
