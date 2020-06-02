<?php

namespace App\Message;

abstract class RebuildStyle
{
    private int $geoObjectId;
    private string $uuid;

    public function __construct(int $geoObjectId, string $uuid)
    {
        $this->geoObjectId = $geoObjectId;
        $this->uuid = $uuid;
    }

    public function getGeoObjectId(): int
    {
        return $this->geoObjectId;
    }

    public function setGeoObjectId(int $geoObjectId): void
    {
        $this->geoObjectId = $geoObjectId;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }
}
