<?php

namespace App\AppMain\Entity\Survey\Survey;

use App\AppMain\Entity\Survey\Evaluation\Definition\Indicator;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="survey_category", schema="x_survey")
 * @ORM\Entity
 */
class Category implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id;

    /**
     * @ORM\OneToMany(targetEntity="App\AppMain\Entity\Survey\Survey\Category", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Survey\Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=true)
     */
    private $parent;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Survey\Survey")
     * @ORM\JoinColumn(referencedColumnName="id", name="survey_id", nullable=false)
     */
    private $survey;

    /**
     * @ORM\OneToMany(targetEntity="App\AppMain\Entity\Survey\Evaluation\Subject\Criterion", mappedBy="category")
     */
    private $criteria;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param mixed $criteria
     */
    public function setCriteria($criteria): void
    {
        $this->criteria = $criteria;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren($children): void
    {
        $this->children = $children;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent): void
    {
        $this->parent = $parent;
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
        return $this->criteria;
    }

    public function setIndicators($indicators): void
    {
        $this->criteria = $indicators;
    }
}
