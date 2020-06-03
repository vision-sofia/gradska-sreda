<?php

namespace App\AppMain\Entity\Survey\Result;

use App\AppMain\Entity\Geospatial\GeoObjectInterface;
use App\AppMain\Entity\Survey;
use App\AppMain\Entity\Survey\Survey\SurveyInterface;
use App\AppMain\Entity\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="result_user_completion",
 *     schema="x_survey"
 * )
 * @ORM\Entity(readOnly=true)
 */
class UserCompletion
{
    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\User\User")
     * @ORM\Id
     * @ORM\JoinColumn(referencedColumnName="id", name="user_id", nullable=false)
     */
    private ?UserInterface $user = null;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\GeoObject")
     * @ORM\JoinColumn(referencedColumnName="id", name="geo_object_id", nullable=false)
     */
    private ?GeoObject $geoObject = null;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Survey\Survey")
     * @ORM\JoinColumn(referencedColumnName="id", name="survey_id", nullable=false)
     */
    private $survey;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isCompleted = false;

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function getGeoObject(): ?GeoObjectInterface
    {
        return $this->geoObject;
    }

    public function getSurvey(): ?SurveyInterface
    {
        return $this->survey;
    }

    public function getIsCompleted(): ?bool
    {
        return $this->isCompleted;
    }
}
