<?php


namespace App\AppMain\Entity\Survey\Response;

use App\AppMain\Entity\Survey;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="response_answer",
 *     schema="x_survey",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"question_id", "answer_id"})}
 * )
 * @ORM\Entity()
 */
class Answer implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Response\Question", inversedBy="answers")
     * @ORM\JoinColumn(referencedColumnName="id", name="question_id", nullable=false)
     */
    private $question;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Question\Answer")
     * @ORM\JoinColumn(referencedColumnName="id", name="answer_id", nullable=false)
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
}