<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\Survey;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("profile", name="app.user.profile")
     */
    public function index(): Response
    {
        return $this->render('front/user-profile/index.html.twig', [

        ]);
    }
}
