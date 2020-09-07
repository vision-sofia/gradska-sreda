<?php

namespace App\Services\Survey;

use App\AppMain\DTO\QuestionAnswerDTO;
use App\AppMain\DTO\QuestionDTO;
use App\AppMain\DTO\ResponseAnswerDTO;
use Iterator;

class Question
{
    /**
     * @param Iterator|QuestionDTO[] $questions
     *
     * @return array<QuestionDTO>
     */
    public function build(Iterator $questions, array $responseAnswers): array
    {
        $re = [];

        foreach ($questions as $question) {
            $a = [];

            $question->setIsAnswered(isset($responseAnswers[$question->getId()]));

            foreach (json_decode($question->getAnswers(), false) as $object) {
                $answerDTO = QuestionAnswerDTO::fromStd($object);

                if (isset($responseAnswers[$question->getId()][$answerDTO->getId()])) {
                    /** @var ResponseAnswerDTO $response */
                    $response = $responseAnswers[$question->getId()][$answerDTO->getId()];

                    $answerDTO->setExplanation($response->getExplanation());
                    $answerDTO->setPhoto($response->getPhoto());
                    $answerDTO->setIsSelected(true);
                }

                $a[] = $answerDTO;
            }

            $question->setAnswers($a);

            $re[] = $question;
        }

        return $re;
    }
}
