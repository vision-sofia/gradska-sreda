<?php

namespace App\AppMain\Entity\Survey\Evaluation\Subject;

use App\AppMain\Entity\Survey\Survey\Category;
use App\AppMain\Entity\Survey\Survey\Survey;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ev_criterion_subject", schema="x_survey")
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
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Survey\Category", inversedBy="criteria")
     * @ORM\JoinColumn(referencedColumnName="id", name="category_id", nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\AppMain\Entity\Survey\Evaluation\Subject\Indicator", mappedBy="criterion")
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

    public function getGroup(): ?Survey
    {
        return $this->category;
    }

    public function setGroup(Category $survey): void
    {
        $this->category = $survey;
    }


    public function getCategory()
    {
        return $this->category;
    }


    public function setCategory($category): void
    {
        $this->category = $category;
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
