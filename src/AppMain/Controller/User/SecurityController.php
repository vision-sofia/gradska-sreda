<?php

namespace App\AppMain\Controller\User;

use App\AppMain\Entity\Survey;
use App\AppMain\Form\Type\User\UserPasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("settings/", name="app.user.settings.")
 * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
 */
class SecurityController extends AbstractController
{
    protected UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("security", name="security")
     */
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());

            $user->setPassword($password);

            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('front/user/security/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
