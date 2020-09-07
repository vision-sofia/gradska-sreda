<?php

namespace App\EventSubscriber;

use App\Event\GeoObjectSurveyTouch;
use App\Services\Survey\Result\CriterionCompletion;
use App\Services\Survey\Result\GeoObjectRating;
use App\Services\Survey\Result\UserCompletion;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SurveyResultSubscriber implements EventSubscriberInterface
{
    protected CriterionCompletion $criterionCompletion;
    protected UserCompletion $userCompletion;
    protected GeoObjectRating $geoObjectRating;

    public function __construct(
        CriterionCompletion $criterionCompletion,
        UserCompletion $userCompletion,
        GeoObjectRating $geoObjectRating
    ) {
        $this->criterionCompletion = $criterionCompletion;
        $this->userCompletion = $userCompletion;
        $this->geoObjectRating = $geoObjectRating;
    }

    /**
     * @return array<string, array>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            GeoObjectSurveyTouch::NAME => [
                ['criterionCompletionUpdate', 30],
                ['userCompletionUpdate', 20],
                ['geoObjectRatingUpdate', 10],
            ],
        ];
    }

    public function criterionCompletionUpdate(GeoObjectSurveyTouch $event): void
    {
        $userId = $event->getUser()->getId();
        $geoObjectId = $event->getGeoObject()->getId();

        if ($geoObjectId && $userId) {
            $this->criterionCompletion->update($geoObjectId, $userId);
        }
    }

    public function userCompletionUpdate(GeoObjectSurveyTouch $event): void
    {
        $userId = $event->getUser()->getId();
        $geoObjectId = $event->getGeoObject()->getId();

        if ($geoObjectId && $userId) {
            $this->userCompletion->update($geoObjectId, $userId);
            $this->userCompletion->updateLocationResponseTimestamp($geoObjectId, $userId);
        }
    }

    public function geoObjectRatingUpdate(GeoObjectSurveyTouch $event): void
    {
        $userId = $event->getUser()->getId();
        $geoObjectId = $event->getGeoObject()->getId();

        if ($geoObjectId && $userId) {
            $this->geoObjectRating->update($geoObjectId, $userId);
        }
    }
}
