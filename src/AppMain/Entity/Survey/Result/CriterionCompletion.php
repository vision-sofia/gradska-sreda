<?php

namespace App\AppMain\Entity\Survey\Result;

use App\AppMain\Entity\Geospatial\GeoObjectInterface;
use App\AppMain\Entity\Survey;
use App\AppMain\Entity\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="result_criterion_completion",
 *     schema="x_survey",
 *     indexes={@ORM\Index(columns={"subject_id"})}
 * )
 * @ORM\Entity(readOnly=true)
 */
class CriterionCompletion
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
     * @ORM\JoinColumn(referencedColumnName="id", name="subject_id", nullable=false)
     */
    private $subject;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isComplete;

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function getGeoObject(): ?GeoObjectInterface
    {
        return $this->geoObject;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getIsComplete(): ?bool
    {
        return $this->isComplete;
    }
}
