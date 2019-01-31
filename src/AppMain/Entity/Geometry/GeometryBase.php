<?php

namespace App\AppMain\Entity\Geometry;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="geometry_base",
 *     schema="x_geometry",
 *     indexes={@ORM\Index(columns={"coordinates"})}
 * )
 * @ORM\Entity()
 */
class GeometryBase extends AbstractGeometry
{
}
