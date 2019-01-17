<?php


namespace App\EventSubscriber;

use App\Event\GeoObjectSurveyTouch;
use App\Services\Survey\Result\CriterionCompletion;
use App\Services\Survey\Result\UserCompletion;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class SurveyResultSubscriber implements EventSubscriberInterface
{
    protected $criterionCompletion;
    protected $userCompletion;

    public function __construct(
        CriterionCompletion $criterionCompletion,
        UserCompletion $userCompletion
    ) {
        $this->criterionCompletion = $criterionCompletion;
        $this->userCompletion = $userCompletion;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            GeoObjectSurveyTouch::NAME => [
                ['criterionCompletionUpdate', 20],
                ['userCompletionUpdate', 10],
            ],
        ];
    }

    public function criterionCompletionUpdate(GeoObjectSurveyTouch $event): void
    {
        $userId = $event->getUser()->getId();
        $geoObjectId = $event->getGeoObject()->getId();

        $this->criterionCompletion->update($geoObjectId, $userId);
    }

    public function userCompletionUpdate(GeoObjectSurveyTouch $event): void
    {
        $userId = $event->getUser()->getId();
        $geoObjectId = $event->getGeoObject()->getId();

        $this->userCompletion->update($geoObjectId, $userId);
    }

}