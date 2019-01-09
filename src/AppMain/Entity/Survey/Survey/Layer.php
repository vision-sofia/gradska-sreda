<?php

namespace App\AppMain\Entity\Survey\Survey;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="object_layer", schema="x_survey")
 * @ORM\Entity()
 */
class Layer implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Survey\Category")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Geospatial\Layer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $layer;


    public function getId(): int
    {
        return $this->id;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category): void
    {
        $this->category = $category;
    }

    public function getLayer()
    {
        return $this->layer;
    }

    public function setLayer($layer): void
    {
        $this->layer = $layer;
    }
}
