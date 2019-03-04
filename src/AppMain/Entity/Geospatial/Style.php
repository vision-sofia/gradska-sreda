<?php

namespace App\AppMain\Entity\Geospatial;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="style", schema="x_geospatial")
 * @ORM\Entity()
 */
class Style
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $styleOption;

    /**
     * @ORM\Column(type="string")
     */
    private $styleValue;

    /**
     * @ORM\Column(type="string")
     */
    private $attribute;

    /**
     * @ORM\Column(type="string")
     */
    private $value;

    /**
     * @ORM\Column(type="string")
     */
    private $code;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStyleOption(): ?string
    {
        return $this->styleOption;
    }

    public function setStyleOption(string $styleOption): void
    {
        $this->styleOption = $styleOption;
    }

    public function getStyleValue(): ?string
    {
        return $this->styleValue;
    }

    public function setStyleValue(string $styleValue): void
    {
        $this->styleValue = $styleValue;
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }
}
