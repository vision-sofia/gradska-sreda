<?php

namespace App\AppMain\Entity\Survey\GeoCollection;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Table(
 *     name="gc_collection_content",
 *     schema="x_survey",
 *     uniqueConstraints={@UniqueConstraint(columns={"geo_collection_id", "geo_object_id"})}
 * )
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Entry implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\GeoCollection\Collection", inversedBy="entries")
     * @ORM\JoinColumn(referencedColumnName="id", name="geo_collection_id", nullable=false)
     */
    private $collection;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\GeoObject")
     * @ORM\JoinColumn(referencedColumnName="id", name="geo_object_id", nullable=false)
     */
    private $geoObject;

    /**
     * @ORM\Column(type="datetime")
     */
    private $addedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function setCollection($collection): void
    {
        $this->collection = $collection;
    }

    public function getGeoObject()
    {
        return $this->geoObject;
    }

    public function setGeoObject($geoObject): void
    {
        $this->geoObject = $geoObject;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist(): void
    {
        $this->addedAt = new \DateTime();
    }
}
