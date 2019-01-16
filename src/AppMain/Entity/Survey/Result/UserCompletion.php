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
     * @ORM\Column(type="json_array", options={"jsonb": true}, nullable=true)
     */
    private $data;

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function getCompletion()
    {
        return $this->completion;
    }

    public function setCompletion($completion): void
    {
        $this->completion = $completion;
    }
}