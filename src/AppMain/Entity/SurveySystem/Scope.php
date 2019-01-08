<?php

namespace App\AppMain\Entity\SurveySystem;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="scope", schema="x_survey")
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
     * @ORM\ManyToOne(targetEntity="\App\AppMain\Entity\SurveySystem\Survey\Survey")
     * @ORM\JoinColumn(referencedColumnName="id", name="survey_id")
     */
    private $survey;

    /**
     * @ORM\ManyToOne(targetEntity="\App\AppMain\Entity\Geospatial\GeospatialObject")
     * @ORM\JoinColumn(referencedColumnName="id", name="geospatial_object_id")
     */
    private $geospatialObject;
}
