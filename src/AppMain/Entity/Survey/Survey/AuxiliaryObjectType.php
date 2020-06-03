<?php

namespace App\AppMain\Entity\Survey\Survey;

use App\AppMain\Entity\Geospatial\ObjectType;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="survey_auxiliary_object_type",
 *     schema="x_survey",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"survey_id", "object_type_id"})}
 * )
 * @ORM\Entity
 */
class AuxiliaryObjectType implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Survey\Survey")
     * @ORM\JoinColumn(referencedColumnName="id", name="survey_id", nullable=true)
     */
    private ?Survey $survey = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\ObjectType")
     * @ORM\JoinColumn(referencedColumnName="id", name="object_type_id", nullable=false)
     */
    private ?ObjectType $geoObjectType = null;

    /**
     * @ORM\Column(type="string")
     */
    private $behavior;

    public function getId(): int
    {
        return $this->id;
    }

    public function getSurvey(): ?Survey
    {
        return $this->survey;
    }

    public function setSurvey(Survey $survey): void
    {
        $this->survey = $survey;
    }

    public function getObjectType(): ?ObjectType
    {
        return $this->geoObjectType;
    }

    public function setGeoObjectType(ObjectType $objectType): void
    {
        $this->geoObjectType = $objectType;
    }

    public function getBehavior()
    {
        return $this->behavior;
    }

    public function setBehavior(string $behavior): void
    {
        $this->behavior = $behavior;
    }
}
