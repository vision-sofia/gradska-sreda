<?php

namespace App\AppMain\Entity\Survey\Result;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Geospatial\GeoObjectInterface;
use App\AppMain\Entity\Survey;
use App\AppMain\Entity\Survey\Evaluation\Subject;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

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
    private ?UserInterface $user = null;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\GeoObject")
     * @ORM\JoinColumn(referencedColumnName="id", name="geo_object_id", nullable=false)
     */
    private ?GeoObject $geoObject = null;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Evaluation\Subject\Criterion")
     * @ORM\JoinColumn(referencedColumnName="id", name="subject_id", nullable=false)
     */
    private ?Subject\Criterion $subject;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isComplete = false;

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function getGeoObject(): ?GeoObjectInterface
    {
        return $this->geoObject;
    }

    public function getSubject(): ?Subject\Criterion
    {
        return $this->subject;
    }

    public function getIsComplete(): ?bool
    {
        return $this->isComplete;
    }
}
