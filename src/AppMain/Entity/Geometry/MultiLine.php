<?php

namespace App\AppMain\Entity\Geometry;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="multiline",
 *     schema="x_geometry",
 *     indexes={@ORM\Index(columns={"coordinates"}, flags={"spatial"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_B8D4B651D17F50A3", columns={"uuid"})}
 * )
 * @ORM\Entity()
 */
class MultiLine extends AbstractGeometry
{
}
