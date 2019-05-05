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
     * @param array $responseAnswers
     * @return array
     */
    public function build(Iterator $questions, array $responseAnswers) {
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