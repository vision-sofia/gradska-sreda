<?php

namespace App\AppMain\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("", name="app.map")
     * @Route("/map", name="app.map-e")
     */
    public function index(): Response
    {
        $center = $this->session->get('center');
        $zoom = $this->session->get('zoom');

        return $this->render('front/map/index.html.twig', [
            'center' => $center,
            'zoom' => $zoom,
        ]);
    }
}
