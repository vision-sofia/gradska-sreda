<?php

namespace App\AppMain\Entity\Survey\Survey;

use App\AppMain\Entity\Geospatial\ObjectType;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="survey_element", schema="x_survey")
 * @ORM\Entity
 */
class Element implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Survey\Category")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\ObjectType")
     * @ORM\JoinColumn(referencedColumnName="id", name="object_type_id", nullable=false)
     */
    private $objectType;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function getObjectType(): ?ObjectType
    {
        return $this->objectType;
    }

    public function setObjectType(ObjectType $objectType): void
    {
        $this->objectType = $objectType;
    }
}
