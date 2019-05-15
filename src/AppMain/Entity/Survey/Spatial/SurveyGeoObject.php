<?php

namespace App\AppMain\Entity\Survey\Spatial;

use App\AppMain\Entity\Geospatial\GeoObjectInterface;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="spatial_geo_object", schema="x_survey")
 * @ORM\Entity(readOnly=true, repositoryClass="App\AppMain\Repository\Survey\Spatial\SurveyGeoObjectRepository")
 */
class SurveyGeoObject implements UuidInterface, GeoObjectInterface
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="\App\AppMain\Entity\Geospatial\GeoObject")
     * @ORM\JoinColumn(referencedColumnName="id", name="geo_object_id", nullable=false)
     */
    private $id;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="\App\AppMain\Entity\Survey\Survey\Survey")
     * @ORM\JoinColumn(referencedColumnName="id", name="survey_id", nullable=false)
     */
    private $survey;

    /**
     * @ORM\Column(type="json_array", options={"jsonb": true, "default" = "{}"})
     */
    private $properties;

    /**
     * @ORM\Column(type="string", name="geo_object_name")
     */
    private $name;

    /**
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="integer")
     */
    private $objectTypeId;

    /**
     * @ORM\Column(type="string")
     */
    private $objectTypeName;

    /**
     * @ORM\Column(type="json_array", options={"jsonb": true, "default"="{}"})
     */
    private $metadata;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $baseStyle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $hoverStyle;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAttributes()
    {
        return $this->properties;
    }

    public function setAttributes($properties): void
    {
        $this->properties = $properties;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function getStyleBase(): ?string
    {
        return $this->baseStyle;
    }

    public function setStyleBase(?string $baseStyle): void
    {
        $this->baseStyle = $baseStyle;
    }

    public function getStyleHover(): ?string
    {
        return $this->hoverStyle;
    }

    public function setStyleHover(?string $hoverStyle): void
    {
        $this->hoverStyle = $hoverStyle;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid($uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getProperties(): ?array
    {
        return $this->properties;
    }


}
