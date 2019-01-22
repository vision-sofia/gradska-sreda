<?php

namespace App\AppMain\Controller\User;

use App\AppMain\Entity\Survey;
use App\AppMain\Form\Type\User\UserProfileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("settings/", name="app.user.settings.")
 * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("profile", name="profile")
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(UserProfileType::class, $this->getUser());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('front/user/profile/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
