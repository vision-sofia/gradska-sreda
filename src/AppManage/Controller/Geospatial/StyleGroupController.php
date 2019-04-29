<?php

namespace App\AppManage\Controller\Geospatial;

use App\AppMain\Entity\Geospatial\StyleGroup;
use App\AppManage\Form\Type\StyleGroupType;
use App\Services\FlashMessage\FlashMessage;
use App\Services\Geospatial\Style;
use App\Services\Geospatial\StyleBuilder\StyleBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/geospatial/style-group", name="manage.geospatial.style-group.")
 */
class StyleGroupController extends AbstractController
{
    protected $styleService;
    protected $flashMessage;
    protected $translator;
    protected $styleBuilder;

    public function __construct(
        Style $styleService,
        FlashMessage $flashMessage,
        TranslatorInterface $translator,
        StyleBuilder $styleBuilder
    ) {
        $this->styleService = $styleService;
        $this->flashMessage = $flashMessage;
        $this->translator = $translator;
        $this->styleBuilder = $styleBuilder;
    }

    /**
     * @Route("", name="list", methods="GET")
     */
    public function list(): Response
    {
        $styleGroups = $this->getDoctrine()
            ->getRepository(StyleGroup::class)
            ->findBy([
                'isForInternalSystem' => true
            ], [
                'code' => 'DESC',
            ]);

        return $this->render('manage/geospatial/style/group/list.html.twig', [
            'styleGroups' => $styleGroups,
        ]);
    }

    /**
     * @Route("/{id}", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, StyleGroup $styleGroup): Response
    {
        $form = $this->createForm(StyleGroupType::class, $styleGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $styles = $request->request->get('style');

            $styleGroup->setStyle($this->styleService->textToGroupStyle($styles));

            $this->getDoctrine()->getManager()->flush();

            $this->flashMessage->addSuccess(
                '',
                $this->translator->trans('flash.edit.success')
            );

            return $this->redirectToRoute('manage.geospatial.style-group.edit', ['id' => $styleGroup->getId()]);
        }

        return $this->render('manage/geospatial/style/group/edit.html.twig', [
            'form' => $form->createView(),
            'style' => $this->styleService->styleToText($styleGroup->getStyle()),
            'styleGroup' => $styleGroup
        ]);
    }
}
