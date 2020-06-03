<?php

namespace App\AppMain\Controller\User;

use App\AppMain\Entity\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("users", name="app.users.")
 */
class PublicProfileController extends AbstractController
{
    /**
     * @Route("/{id}", name="profile")
     */
    public function index(Request $request, User $user): Response
    {
        return new JsonResponse([]);
    }
}
