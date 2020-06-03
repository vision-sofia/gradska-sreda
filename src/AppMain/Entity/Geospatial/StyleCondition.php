<?php

namespace App\AppMain\Entity\Geospatial;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="style_condition", schema="x_geospatial")
 * @ORM\Entity
 */
class StyleCondition
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $attribute = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $value = null;

    /**
     * @ORM\Column(type="json", options={"jsonb": true}, nullable=true)
     */
    private ?array $baseStyle = null;

    /**
     * @ORM\Column(type="json", options={"jsonb": true}, nullable=true)
     */
    private ?array $hoverStyle = null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $priority = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isDynamic = false;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttribute(): ?string
    {
        return $this->attribute;
    }

    public function setAttribute(string $attribute): void
    {
        $this->attribute = $attribute;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getBaseStyle(): ?array
    {
        return $this->baseStyle;
    }

    public function setBaseStyle(?array $baseStyle): void
    {
        $this->baseStyle = $baseStyle;
    }

    public function getHoverStyle(): ?array
    {
        return $this->hoverStyle;
    }

    public function setHoverStyle(?array $hoverStyle): void
    {
        $this->hoverStyle = $hoverStyle;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    public function getIsDynamic(): ?bool
    {
        return $this->isDynamic;
    }

    public function setIsDynamic(bool $isDynamic): void
    {
        $this->isDynamic = $isDynamic;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
