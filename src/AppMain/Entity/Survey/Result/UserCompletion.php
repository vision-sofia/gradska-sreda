<?php


namespace App\AppMain\Entity\Survey\Result;

use App\AppMain\Entity\Survey;
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
    private $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\GeoObject")
     * @ORM\JoinColumn(referencedColumnName="id", name="geo_object_id", nullable=false)
     */
    private $geoObject;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Survey\Survey")
     * @ORM\JoinColumn(referencedColumnName="id", name="survey_id", nullable=false)
     */
    private $survey;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCompleted;
}