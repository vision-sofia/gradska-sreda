<?php

namespace App\AppMain\Entity\Survey\GeoCollection;

use App\AppMain\Entity\Survey\Survey\Survey;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\User\UserInterface;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="gc_collection", schema="x_survey")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Collection implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\User\User")
     * @ORM\JoinColumn(referencedColumnName="id", name="user_id", nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Survey\Survey")
     * @ORM\JoinColumn(referencedColumnName="id", name="survey_id", nullable=false)
     */
    private $survey;

    /**
     * @ORM\OneToMany(targetEntity="App\AppMain\Entity\Survey\GeoCollection\Entry", mappedBy="collection")
     */
    private $entries;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getSurvey(): ?Survey
    {
        return $this->survey;
    }

    public function setSurvey(Survey $survey): void
    {
        $this->survey = $survey;
    }

    public function getEntries()
    {
        return $this->entries;
    }

    public function setEntries($entries): void
    {
        $this->entries = $entries;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTime();
    }
}
