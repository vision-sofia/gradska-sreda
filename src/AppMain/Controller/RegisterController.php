<?php

namespace App\AppMain\Controller;


use App\AppMain\Entity\User\User;

use App\AppMain\Form\UserRegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    protected $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/register", name="app.register")
     */
    public function signUp(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());

            $user->setPassword($password);
            $user->setIsActive(true);
            $user->addRole('ROLE_USER');

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Регистрацията е успешна!');

            return $this->redirectToRoute('app.homepage');
        }

        return $this->render('front/register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
