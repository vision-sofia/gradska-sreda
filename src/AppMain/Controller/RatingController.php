<?php

namespace App\AppMain\Controller;

use App\Services\Rating\SurveyCompletionRating;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends AbstractController
{
    private SurveyCompletionRating $surveyCompletionRating;

    public function __construct(SurveyCompletionRating $surveyCompletionRating)
    {
        $this->surveyCompletionRating = $surveyCompletionRating;
    }

    /**
     * @Route("rating", name="app.rating")
     */
    public function index(): Response
    {
        $rating = $this->surveyCompletionRating->getRating();

        return $this->render('front/rating/index.html.twig', [
            'rating' => $rating,
        ]);
    }
}
