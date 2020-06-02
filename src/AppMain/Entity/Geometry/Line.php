<?php

namespace App\AppMain\Entity\Geometry;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="line",
 *     schema="x_geometry",
 *     indexes={@ORM\Index(columns={"coordinates"}, flags={"spatial"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_B8D4B651D17F50A4", columns={"uuid"})}
 * )
 * @ORM\Entity
 */
class Line extends AbstractGeometry
{
}
