<?php


namespace App\AppMain\Entity\Achievement;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="a_survey_completion_achievement", schema="x_main")
 * @ORM\Entity()
 */
class CategoryCompletionAchievement implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Survey\Category")
     * @ORM\JoinColumn(nullable=false)
     */
    private $surveyCategory;

    /**
     * @ORM\Column(type="decimal", scale=1, precision=2)
     */
    private $threshold;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geometry\Polygon")
     * @ORM\JoinColumn(nullable=true)
     */
    private $zone;
}
