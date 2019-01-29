<?php


namespace App\AppMain\Entity\Geospatial;

use App\AppMain\Entity\Geometry\GeometryBase;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;


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
    private $targetName;

    /**
     * @ORM\Column(type="string")
     */
    private $targetValue;

    /**
     * @ORM\Column(type="string")
     */
    private $group;

    /**
     * @ORM\Column(type="string")
     */
    private $key;

    /**
     * @ORM\Column(type="string")
     */
    private $value;
}