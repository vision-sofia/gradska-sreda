<?php

namespace App\AppMain\Entity\Survey\Response;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Geospatial\GeoObjectInterface;
use App\AppMain\Entity\Survey;
use App\AppMain\Entity\Survey\Question\Question as SurveyQuestion;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(
 *     name="response_question",
 *     schema="x_survey",
 *     uniqueConstraints={@ORM\UniqueConstraint(
 *         columns={"user_id", "question_id", "geo_object_id"},
 *     options={"where": "(is_latest IS TRUE)"})})
 *     }
 * )
 *     @ORM\Entity
 *     @ORM\HasLifecycleCallbacks
 */
class Question implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\User\User")
     * @ORM\JoinColumn(referencedColumnName="id", name="user_id", nullable=false)
     */
    private ?UserInterface $user = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Response\Location", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", name="location_id", nullable=true)
     */
    private ?Location $location;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Question\Question")
     * @ORM\JoinColumn(referencedColumnName="id", name="question_id", nullable=false)
     */
    private ?SurveyQuestion $question = null;

    /**
     * @ORM\OneToMany(targetEntity="App\AppMain\Entity\Survey\Response\Answer", mappedBy="question", cascade={"persist"})
     */
    private Collection $answers;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\GeoObject")
     * @ORM\JoinColumn(referencedColumnName="id", name="geo_object_id", nullable=false)
     */
    private ?GeoObjectInterface $geoObject = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $answeredAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isLatest = false;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private bool $isCompleted = false;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function addAnswer(Survey\Response\Answer $answer): void
    {
        $answer->setQuestion($this);

        $this->answers[] = $answer;
    }

    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function getQuestion(): ?Survey\Question\Question
    {
        return $this->question;
    }

    public function setQuestion(Survey\Question\Question $question): void
    {
        $this->question = $question;
    }

    public function getGeoObject()
    {
        return $this->geoObject;
    }

    public function setGeoObject(GeoObjectInterface $geoObject): void
    {
        $this->geoObject = $geoObject;
    }

    public function getAnsweredAt()
    {
        return $this->answeredAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getIsLatest(): bool
    {
        return $this->isLatest;
    }

    public function setIsLatest(bool $isLatest): void
    {
        $this->isLatest = $isLatest;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($location): void
    {
        $this->location = $location;
    }

    public function getIsCompleted(): ?bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): void
    {
        $this->isCompleted = $isCompleted;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist(): void
    {
        $this->answeredAt = new \DateTimeImmutable();
    }

    /**
     * @ORM\PrePersist
     */
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
