<?php


namespace App\AppMain\Entity\Geospatial;


interface GeoObjectInterface
{
    public function getId();

    public function setName(string $name);

    public function getName(): ?string;
}