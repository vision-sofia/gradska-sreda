<?php


namespace App\AppMain\Entity\Survey\Response;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Geospatial\GeoObjectInterface;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;


use App\AppMain\Entity\Survey;

/**
 * @ORM\Table(
 *     name="response_location",
 *     schema="x_survey",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"user_id", "geo_object_id"}, options={"where": "(coordinates IS NULL)"}),
 *          @ORM\UniqueConstraint(columns={"user_id", "geo_object_id", "coordinates"}, options={"where": "(coordinates IS NOT NULL)"})
 *     }
 * )
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Location implements UuidInterface
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
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\GeoObject")
     * @ORM\JoinColumn(referencedColumnName="id", name="geo_object_id", nullable=false)
     */
    private $geoObject;

    /**
     * @ORM\Column(name="coordinates", type="geography", options={"geometry_type"="POINT"}, nullable=true)
     */
    private $coordinates;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;


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

    /**
     * @ORM\PrePersist()
     */
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}