<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\User\User;
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
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('front/info/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("about", name="app.about")
     */
    public function about(): Response
    {
        return $this->render('front/about/index.html.twig', [
        ]);
    }
}
