<?php

namespace App\AppMain\Entity\Achievement;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="a_photo_upload_achievement", schema="x_main")
 * @ORM\Entity
 */
class UploadPhotoAchievement extends AbstractAchievement
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="App\Doctrine\AchievementIdGenerator")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Survey\Survey")
     * @ORM\JoinColumn(nullable=false)
     */
    private $survey;

    /**
     * @ORM\Column(type="smallint")
     */
    private $threshold;

    public function getSurvey()
    {
        return $this->survey;
    }

    public function setSurvey($survey): void
    {
        $this->survey = $survey;
    }

    public function getThreshold(): ?int
    {
        return $this->threshold;
    }

    public function setThreshold(int $threshold): void
    {
        $this->threshold = $threshold;
    }
}
