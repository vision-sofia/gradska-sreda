<?php

namespace App\AppMain\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{
    /**
     * @Route("info", name="app.info")
     */
    public function index(): Response
    {
        return $this->render('front/info/index.html.twig', [
        ]);
    }
}
