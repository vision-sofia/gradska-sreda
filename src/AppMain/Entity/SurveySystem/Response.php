<?php


namespace App\AppMain\Entity\SurveySystem;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="response", schema="x_survey")
 * @ORM\Entity()
 */
class Response implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\User\User")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\SurveySystem\Question\Answer")
     * @ORM\JoinColumn(referencedColumnName="id", name="answer_id", nullable=false)
     */
    private $answer;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\SurveySystem\Survey\Survey")
     * @ORM\JoinColumn(referencedColumnName="id", name="survey_id", nullable=false)
     */
    private $survey;

    public function getId(): int
    {
        return $this->id;
    }
}