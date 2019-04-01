<?php

namespace App\AppManage\Controller;

use App\AppManage\Entity\Settings;
use App\AppManage\Form\Type\SettingsType;
use App\Services\FlashMessage\FlashMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/settings", name="manage.settings.")
 */
class SettingsController extends AbstractController
{
    protected $flashMessage;
    protected $translator;

    public function __construct(
        FlashMessage $flashMessage,
        TranslatorInterface $translator
    ) {
        $this->flashMessage = $flashMessage;
        $this->translator = $translator;
    }

    /**
     * @Route("", name="list", methods="GET")
     */
    public function list(): Response
    {
        $settings = $this->getDoctrine()
            ->getRepository(Settings::class)
            ->findBy([], ['key' => 'DESC']);

        return $this->render('manage/settings/list.html.twig', [
            'settings' => $settings,
        ]);
    }

    /**
     * @Route("/{key}", name="edit", methods={"GET", "POST"})
     * @ParamConverter("settings", class="App\AppManage\Entity\Settings", options={"mapping": {"key": "key"}})
     */
    public function edit(Request $request, Settings $settings): Response
    {
        $form = $this->createForm(SettingsType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->flashMessage->addSuccess(
                '',
                $this->translator->trans('flash.edit.success')
            );

            return $this->redirectToRoute('manage.settings.list');
        }

        return $this->render('manage/settings/edit.html.twig', [
            'settings' => $settings,
            'form' => $form->createView(),
        ]);
    }
}
