<?php

namespace App\AppMain\Entity\Geometry;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="point",
 *     schema="x_geometry",
 *     indexes={@ORM\Index(columns={"coordinates"})},*
 *     uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_B8D4B651D17F50A2", columns={"uuid"})}
 * )
 * @ORM\Entity()
 */
class Point extends AbstractGeometry
{
}
