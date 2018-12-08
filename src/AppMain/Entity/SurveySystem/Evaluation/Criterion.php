<?php

namespace App\AppMain\Entity\SurveySystem\Evaluation;

use App\AppMain\Entity\SurveySystem\Survey\Survey;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ed_criterion", schema="x_survey")
 * @ORM\Entity()
 */
class Criterion implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\SurveySystem\Survey\Survey")
     * @ORM\JoinColumn(referencedColumnName="id", name="survey_id", nullable=false)
     */
    private $survey;


    /**
     * @ORM\OneToMany(targetEntity="App\AppMain\Entity\SurveySystem\Evaluation\Indicator", mappedBy="criterion")
     */
    private $indicators;

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurvey(): ?Survey
    {
        return $this->survey;
    }

    public function setSurvey(Survey $survey): void
    {
        $this->survey = $survey;
    }

    /**
     * @return Collection|Indicator[]
     */
    public function getIndicators(): Collection
    {
        return $this->indicators;
    }

    public function setIndicators($indicators): void
    {
        $this->indicators = $indicators;
    }
}
