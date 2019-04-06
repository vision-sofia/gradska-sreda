<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\User\User;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends AbstractController
{
    /**
     * @Route("rating", name="app.rating")
     */
    public function index(): Response
    {
        return $this->render('front/rating/index.html.twig', [
            'rating' => $this->getStat()
        ]);
    }


    private function getStat():\Generator
    {
        /** @var Connection $conn */
        $conn = $this->getDoctrine()->getConnection();

        $stmt = $conn->prepare('
            WITH z AS (
              SELECT
                  count(*) filter (where is_completed = true)::dec as completed,
                  count(*)::dec as total,
                  username
              FROM
                  x_survey.result_user_completion r
                      INNER JOIN
                  x_main.user_base u ON r.user_id = u.id
              GROUP BY
                  u.id
            )
            SELECT 
                total, 
                completed, 
                ((total/2)+(completed+(completed*0.5)))/2 as score, 
                username
            FROM 
                z 
            ORDER BY 
                score DESC
        ');

        $stmt->execute();

        while ($row = $stmt->fetch()) {
            yield $row;
        }
    }
}
