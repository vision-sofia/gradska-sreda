<?php

namespace App\AppMain\Entity\Survey\Result;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey;
use App\AppMain\Entity\Survey\Evaluation\Subject;
use App\AppMain\Entity\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="result_geo_object_rating",
 *     schema="x_survey"
 * )
 * @ORM\Entity(readOnly=true)
 */
class GeoObjectRating
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\User\User")
     * @ORM\JoinColumn(referencedColumnName="id", name="user_id", nullable=false)
     */
    private $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\GeoObject")
     * @ORM\JoinColumn(referencedColumnName="id", name="geo_object_id", nullable=false)
     */
    private $geoObject;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Evaluation\Subject\Criterion")
     * @ORM\JoinColumn(referencedColumnName="id", name="criterion_subject_id", nullable=false)
     */
    private $criterionSubject;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=4)
     */
    private $rating;

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function getGeoObject(): ?GeoObject
    {
        return $this->geoObject;
    }

    public function getCriterionSubject(): ?Subject\Criterion
    {
        return $this->criterionSubject;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }
}
