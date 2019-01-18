<?php


namespace App\AppMain\Entity\Survey\Result;

use App\AppMain\Entity\Survey;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="result_criterion_completion",
 *     schema="x_survey",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"subject_id", "user_id", "geo_object_id"})
 *     },
 *     indexes={@ORM\Index(columns={"subject_id"})}
 * )
 * @ORM\Entity(readOnly=true)
 */
class CriterionCompletion
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Evaluation\Subject\Criterion")
     * @ORM\JoinColumn(referencedColumnName="id", name="subject_id", nullable=false)
     */
    private $subject;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\User\User")
     * @ORM\JoinColumn(referencedColumnName="id", name="user_id", nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\GeoObject")
     * @ORM\JoinColumn(referencedColumnName="id", name="geo_object_id", nullable=false)
     */
    private $geoObject;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isComplete;
}