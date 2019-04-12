<?php

namespace App\AppMain\Entity\Achievement;

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
     * @ORM\CustomIdGenerator(class="App\Doctrine\AchievementIdGenerator")
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

    public function getSurveyCategory()
    {
        return $this->surveyCategory;
    }

    public function setSurveyCategory($surveyCategory): void
    {
        $this->surveyCategory = $surveyCategory;
    }

    public function getThreshold()
    {
        return $this->threshold;
    }

    public function setThreshold($threshold): void
    {
        $this->threshold = $threshold;
    }

    public function getZone()
    {
        return $this->zone;
    }

    public function setZone($zone): void
    {
        $this->zone = $zone;
    }
}
