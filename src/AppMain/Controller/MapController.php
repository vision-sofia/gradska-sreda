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

    /**
     * @Route("/map/p", name="app.map-p")
     */
    public function p(SessionInterface $session): Response
    {
        $ex = explode(',', $session->get('center'));

        if (empty($session->get('center'))) {
            $ex = [42.697664, 23.3166103];
        }

        return new JsonResponse([
            'zoom' => $session->get('zoom'),
            'lat' => (float) $ex[0],
            'lng' => (float) $ex[1],
        ]);
    }

    /**
     * @Route("/map/z", name="app.map-z")
     */
    public function z(Request $request): JsonResponse
    {
        $zoom = $request->query->get('zoom');
        $center = $request->query->get('c');

        $zoom = (float) $zoom;

        $this->session->set('center', $center);
        $this->session->set('zoom', $zoom);

        return  new JsonResponse([]);
    }
}
