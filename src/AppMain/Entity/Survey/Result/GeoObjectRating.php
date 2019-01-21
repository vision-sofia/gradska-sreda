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
 *     schema="x_survey",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"criterion_subject_id", "geo_object_id", "user_id"})
 *     }
 * )
 * @ORM\Entity()
 */
class GeoObjectRating
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Evaluation\Subject\Criterion")
     * @ORM\JoinColumn(referencedColumnName="id", name="criterion_subject_id", nullable=false)
     */
    private $criterionSubject;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\GeoObject")
     * @ORM\JoinColumn(referencedColumnName="id", name="geo_object_id", nullable=false)
     */
    private $geoObject;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\User\User")
     * @ORM\JoinColumn(referencedColumnName="id", name="user_id", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=4)
     */
    private $rating;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function getCriterionSubject(): ?Subject\Criterion
    {
        return $this->criterionSubject;
    }

    public function setCriterionSubject(Subject\Criterion $criterionSubject): void
    {
        $this->criterionSubject = $criterionSubject;
    }

    public function getGeoObject(): ?GeoObject
    {
        return $this->geoObject;
    }

    public function setGeoObject(GeoObject $geoObject): void
    {
        $this->geoObject = $geoObject;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }
}
