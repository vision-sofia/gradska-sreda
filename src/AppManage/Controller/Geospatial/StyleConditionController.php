<?php

namespace App\AppManage\Controller\Geospatial;

use App\AppMain\Entity\Geospatial\StyleCondition;
use App\AppManage\Form\Type\StyleConditionType;
use App\Services\FlashMessage\FlashMessage;
use App\Services\Geospatial\Style;
use App\Services\Geospatial\StyleBuilder\StyleBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/geospatial/style-condition", name="manage.geospatial.style-condition.")
 */
class StyleConditionController extends AbstractController
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
        $styleConditions = $this->getDoctrine()
            ->getRepository(StyleCondition::class)
            ->findBy([], [
                'isDynamic' => 'DESC',
                'attribute' => 'DESC',
                'value' => 'ASC',
            ]);

        return $this->render('manage/geospatial/style/condition/list.html.twig', [
            'styleConditions' => $styleConditions,
        ]);
    }

    /**
     * @Route("/rebuild", name="rebuild", methods="POST")
     */
    public function rebuildStyles(): RedirectResponse
    {
        // TODO: move to task queue
        $this->styleBuilder->build();

        $this->flashMessage->addSuccess(
            '',
            $this->translator->trans('flash.style.rebuild.success')
        );

        return $this->redirectToRoute('manage.geospatial.style-condition.list');
    }

    /**
     * @Route("/{id}", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, StyleCondition $styleCondition): Response
    {
        $form = $this->createForm(StyleConditionType::class, $styleCondition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $styles = $request->request->get('styles');

            if (isset($styles['base'])) {
                $baseStyles = $this->styleService->formatConditionStyle(
                    $styles['base'],
                    $styleCondition->getBaseStyle()
                );

                $styleCondition->setBaseStyle($baseStyles);
            }

            if (isset($styles['hover'])) {
                $hoverStyles = $this->styleService->formatConditionStyle(
                    $styles['hover'],
                    $styleCondition->getHoverStyle()
                );

                $styleCondition->setHoverStyle($hoverStyles);
            }

            $this->getDoctrine()->getManager()->flush();

            $this->flashMessage->addSuccess(
                '',
                $this->translator->trans('flash.edit.success')
            );

            return $this->redirectToRoute('manage.geospatial.style-condition.edit', [
                'id' => $styleCondition->getId()
            ]);
        }

        return $this->render('manage/geospatial/style/condition/edit.html.twig', [
            'form' => $form->createView(),
            'styles' => $this->styleService->conditionStyleToText($styleCondition),
        ]);
    }
}
