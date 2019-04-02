<?php

namespace App\AppManage\Controller\Geospatial;

use App\AppMain\Entity\Geospatial\StyleCondition;
use App\AppManage\Form\Type\StyleConditionType;
use App\Services\FlashMessage\FlashMessage;
use App\Services\Geospatial\Style;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    public function __construct(
        Style $styleService,
        FlashMessage $flashMessage,
        TranslatorInterface $translator
    ) {
        $this->styleService = $styleService;
        $this->flashMessage = $flashMessage;
        $this->translator = $translator;
    }

    /**
     * @Route("", name="list", methods="GET")
     */
    public function list(): Response
    {
        $styleConditions = $this->getDoctrine()
            ->getRepository(StyleCondition::class)
            ->findBy([], [
                'attribute' => 'DESC',
                'value' => 'ASC',
            ])
        ;

        return $this->render('manage/geospatial/style/condition/list.html.twig', [
            'styleConditions' => $styleConditions,
        ]);
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
                $baseStyles = $this->styleService->formatStyle(
                    $styles['base'],
                    $styleCondition->getBaseStyle()
                );

                $styleCondition->setBaseStyle($baseStyles);
            }

            if (isset($styles['hover'])) {
                $hoverStyles = $this->styleService->formatStyle(
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

            return $this->redirectToRoute('manage.geospatial.style-condition.list');
        }

        return $this->render('manage/geospatial/style/condition/edit.html.twig', [
            'form' => $form->createView(),
            'styles' => $this->styleService->toText($styleCondition),
        ]);
    }
}
