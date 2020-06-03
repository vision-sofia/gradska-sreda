<?php

namespace App\AppMain\Entity\Survey\Spatial;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey\Survey\Survey;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="spatial_scope", schema="x_survey")
 * @ORM\Entity
 */
class Scope
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="\App\AppMain\Entity\Survey\Survey\Survey")
     * @ORM\JoinColumn(referencedColumnName="id", name="survey_id", nullable=false)
     */
    private ?Survey $survey = null;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="\App\AppMain\Entity\Geospatial\GeoObject")
     * @ORM\JoinColumn(referencedColumnName="id", name="geo_object_id", nullable=false)
     */
    private ?GeoObject $geoObjectId = null;

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
