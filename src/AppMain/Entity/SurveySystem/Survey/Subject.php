<?php

namespace App\AppMain\Entity\SurveySystem\Survey;

use App\AppMain\Entity\SurveySystem\Question\Question;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="survey_subject", schema="x_survey")
 * @ORM\Entity()
 */
class Subject implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\SurveySystem\Survey\Survey")
     * @ORM\JoinColumn(name="survey_id", referencedColumnName="id", nullable=false)
     */
    private $survey;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\SurveySystem\Question\Question")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=false)
     */
    private $question;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $sort;

    public function getId(): int
    {
        return $this->id;
    }


    public function getSurvey():?Survey
    {
        return $this->survey;
    }


    public function setSurvey(Survey $survey): void
    {
        $this->survey = $survey;
    }

    public function getQuestion():?Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): void
    {
        $this->question = $question;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function setSort(?int $sort): void
    {
        $this->sort = $sort;
    }
}
