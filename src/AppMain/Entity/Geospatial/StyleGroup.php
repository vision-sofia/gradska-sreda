<?php

namespace App\AppMain\Entity\Geospatial;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="style_group",
 *     schema="x_geospatial",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"code"})}
 * )
 * @ORM\Entity
 */
class StyleGroup
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="code", type="string")
     */
    private $code;

    /**
     * @ORM\Column(type="json", options={"jsonb": true})
     */
    private $style;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isForInternalSystem;

    /**
     * @ORM\Column(type="string")
     */
    private $description = '';

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getStyle(): ?array
    {
        return $this->style;
    }

    public function setStyle(array $styles): void
    {
        $this->style = $styles;
    }

    public function getIsForInternalSystem(): ?bool
    {
        return $this->isForInternalSystem;
    }

    public function setIsForInternalSystem(bool $isForInternalSystem): void
    {
        $this->isForInternalSystem = $isForInternalSystem;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
