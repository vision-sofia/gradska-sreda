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
                $this->translator->trans('flash.add.success')
            );

            return $this->redirectToRoute('manage.settings.list');
        }


        $styles = [
            'cat1' => [
                'color' => '#0099ff',
                'opacity' => 0.5,
                'width' => 5,
            ],
            'cat2' => [
                'color' => '#33cc33',
                'opacity' => 0.5,
                'weight' => 5,
            ],
            'cat3' => [
                'color' => '#ff3300',
                'opacity' => 0.5,
                'weight' => 5,
            ],
            'poly' => [
                'stroke' => '#ff3300',
                'strokeWidth' => 5,
                'strokeOpacity' => 0.2,
                'fill' => '#ff00ff',
                'fillOpacity' => 0.5,
            ],
            'line_main' => [
                'color' => '#ff99ff',
                'opacity' => 0.5,
                'width' => 3,
            ],
            'line_hover' => [
                'opacity' => 0.8,
            ],
            'point_default' => [
                'radius' => 8,
                'fillColor' => '#ff7800',
                'color' => '#000',
                'weight' => 1,
                'opacity' => 1,
                'fillOpacity' => 0.8,
            ],
            'point_hover' => [
                'fillColor' => '#ff00ff',
            ],
            'poly_main' => [
                'stroke' => '#ff99ff',
                'strokeWidth' => 1,
                'strokeOpacity' => 0.2,
                'fill' => '#ff00ff',
                'fillOpacity' => 0.05,
            ],
            'poly_hover' => [
                'fillOpacity' => 0.3,
            ],
            'on_dialog_line' => [
                'color' => '#00ffff',
                'opacity' => 0.5,
            ],
            'on_dialog_point' => [
                'fillColor' => '#00ffff',
                'opacity' => 0.5,
            ],
            'on_dialog_polygon' => [
                'fillColor' => '#00ffff',
                'opacity' => 0.5,
            ],
        ];

        dump(json_encode($styles));

        return $this->render('manage/settings/edit.html.twig', [
            'settings' => $settings,
            'form' => $form->createView(),
        ]);
    }
}
