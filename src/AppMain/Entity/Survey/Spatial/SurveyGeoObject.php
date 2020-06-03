<?php

namespace App\AppMain\Entity\Survey\Spatial;

use App\AppMain\Entity\Geospatial\GeoObjectInterface;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="spatial_geo_object",
 *     schema="x_survey",
 *     indexes={@ORM\Index(columns={"zoom"})}
 * )
 * @ORM\Entity(
 *     readOnly=true,
 *     repositoryClass="App\AppMain\Repository\Survey\Spatial\SurveyGeoObjectRepository"
 * )
 */
class SurveyGeoObject implements UuidInterface, GeoObjectInterface
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="\App\AppMain\Entity\Geospatial\GeoObject")
     * @ORM\JoinColumn(referencedColumnName="id", name="geo_object_id", nullable=false)
     */
    private ?int $id = null;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="\App\AppMain\Entity\Survey\Survey\Survey")
     * @ORM\JoinColumn(referencedColumnName="id", name="survey_id", nullable=false)
     */
    private $survey;

    /**
     * @ORM\Column(type="json", options={"jsonb": true, "default": "{}"})
     */
    private ?array $properties = null;

    /**
     * @ORM\Column(type="string", name="geo_object_name")
     */
    private ?string $name = null;

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
     * @ORM\Column(type="json", options={"jsonb": true, "default": "{}"})
     */
    private ?array $metadata = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $baseStyle = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $hoverStyle = null;

    /**
     * @ORM\Column(type="integer[]", nullable=true)
     */
    private ?array $zoom = null;

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

    public function getZoom(): ?array
    {
        return $this->zoom;
    }

    public function setZoom(?array $zoom): void
    {
        $this->zoom = $zoom;
    }
}
