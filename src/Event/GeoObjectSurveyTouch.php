<?php

namespace App\Event;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\User\UserInterface;
use Symfony\Component\EventDispatcher\Event;

class GeoObjectSurveyTouch extends Event
{
    public const NAME = 'geo_object.survey.touch';

    protected $user;
    protected $geoObject;

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
