<?php

namespace App\Event;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class GeoObjectSurveyTouch extends Event
{
    public const NAME = 'geo_object.survey.touch';

    protected UserInterface $user;
    protected GeoObject $geoObject;

    public function __construct(GeoObject $geoObject, UserInterface $user)
    {
        $this->user = $user;
        $this->geoObject = $geoObject;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getGeoObject(): GeoObject
    {
        return $this->geoObject;
    }
}
