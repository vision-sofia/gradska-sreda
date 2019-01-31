<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\Survey;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MapController extends AbstractController
{
    /**
     * @Route("", name="app.map")
     */
    public function index(): Response
    {
        return $this->render('front/map/index.html.twig', [

        ]);
    }
}
