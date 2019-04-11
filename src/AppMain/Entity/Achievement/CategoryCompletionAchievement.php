<?php


namespace App\AppMain\Entity\Achievement;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="a_survey_completion_achievement", schema="x_main")
 * @ORM\Entity()
 */
class CategoryCompletionAchievement extends AbstractAchievement
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="App\Doctrine\NextValGenerator")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Survey\Category")
     * @ORM\JoinColumn(nullable=false)
     */
    private $surveyCategory;

    /**
     * @ORM\Column(type="smallint")
     */
    private $threshold;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geometry\Polygon")
     * @ORM\JoinColumn(nullable=true)
     */
    private $zone;
}
