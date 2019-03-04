<?php

namespace App\AppMain\Entity\Geospatial;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="style_group", schema="x_geospatial")
 * @ORM\Entity()
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
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @ORM\Column(type="json_array", options={"jsonb": true})
     */
    private $styles;

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

    public function getStyles(): ?array
    {
        return $this->styles;
    }

    public function setStyles(array $styles): void
    {
        $this->styles = $styles;
    }
}
