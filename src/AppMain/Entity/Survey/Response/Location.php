<?php

namespace App\AppMain\Entity\Survey\Response;

use App\AppMain\Entity\Geospatial\GeoObjectInterface;
use App\AppMain\Entity\Survey\Survey\Survey;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\User\UserInterface;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="response_location",
 *     schema="x_survey",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"user_id", "geo_object_id"}, options={"where": "(coordinates IS NULL)"}),
 *         @ORM\UniqueConstraint(columns={"user_id", "geo_object_id", "coordinates"}, options={"where": "(coordinates IS NOT NULL)"})
 *     }
 * )
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Location implements UuidInterface
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
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\GeoObject")
     * @ORM\JoinColumn(referencedColumnName="id", name="geo_object_id", nullable=false)
     */
    private ?GeoObjectInterface $geoObject = null;

    /**
     * @ORM\Column(name="coordinates", type="geography", options={"geometry_type": "POINT"}, nullable=true)
     */
    private $coordinates;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private bool $isConfirmed = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Survey\Survey")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Survey $survey;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?\DateTimeInterface $confirmedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCoordinates()
    {
        return $this->coordinates;
    }

    public function setCoordinates($coordinates): void
    {
        $this->coordinates = $coordinates;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function getGeoObject()
    {
        return $this->geoObject;
    }

    public function setGeoObject(GeoObjectInterface $geoObject): void
    {
        $this->geoObject = $geoObject;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): void
    {
        $this->isConfirmed = $isConfirmed;
    }

    public function getConfirmedAt(): ?\DateTimeInterface
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(?\DateTimeInterface $confirmedAt): void
    {
        $this->confirmedAt = $confirmedAt;
    }

    public function getSurvey(): ?Survey
    {
        return $this->survey;
    }

    public function setSurvey(?Survey $survey): void
    {
        $this->survey = $survey;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
