<?php

namespace App\AppMain\Entity\Survey\Survey;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="survey_scope", schema="x_survey")
 * @ORM\Entity()
 */
class Scope implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\App\AppMain\Entity\Survey\Survey\Survey")
     * @ORM\JoinColumn(referencedColumnName="id", name="survey_id", nullable=false)
     */
    private $survey;

    /**
     * @ORM\ManyToOne(targetEntity="\App\AppMain\Entity\Geospatial\GeoObject")
     * @ORM\JoinColumn(referencedColumnName="id", name="geo_object_id", nullable=false)
     */
    private $geoObjectId;

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

    public function getGeoObjectId(): ?GeoObject
    {
        return $this->geoObjectId;
    }

    public function setGeoObjectId(GeoObject $geoObjectId): void
    {
        $this->geoObjectId = $geoObjectId;
    }
}
