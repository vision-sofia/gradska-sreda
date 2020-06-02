<?php

namespace App\MessageHandler;

use App\AppMain\Entity\Survey\Survey\Survey;
use App\Message\RebuildStyle;
use App\Message\RebuildStyleByAnswer;
use App\Message\RebuildStyleByQuestion;
use App\Services\Geospatial\StyleBuilder\StyleBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RebuildStyleHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private StyleBuilder $styleBuilder;

    public function __construct(EntityManagerInterface $entityManager, StyleBuilder $styleBuilder)
    {
        $this->entityManager = $entityManager;
        $this->styleBuilder = $styleBuilder;
    }

    public function __invoke(RebuildStyle $rebuildStyle)
    {
        if ($rebuildStyle instanceof RebuildStyleByAnswer) {
            $surveyId = $this->entityManager
                ->getRepository(Survey::class)
                ->findSurveyIdByAnswerUuid($rebuildStyle->getUuid())
            ;
        } elseif ($rebuildStyle instanceof RebuildStyleByQuestion) {
            $surveyId = $this->entityManager
                ->getRepository(Survey::class)
                ->findSurveyIdByQuestionUuid($rebuildStyle->getUuid())
            ;
        } else {
            throw new \LogicException();
        }

        $this->styleBuilder->buildSingle($surveyId, $rebuildStyle->getGeoObjectId());
    }
}
