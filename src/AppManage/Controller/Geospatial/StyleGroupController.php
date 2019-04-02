<?php

namespace App\AppManage\Controller\Geospatial;

use App\AppMain\Entity\Geospatial\StyleGroup;
use App\AppManage\Form\Type\StyleGroupType;
use App\Services\FlashMessage\FlashMessage;
use App\Services\Geospatial\Style;
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
        $styleGroups = $this->getDoctrine()
            ->getRepository(StyleGroup::class)
            ->findBy([], [
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

            $result = [];

            $lines = explode("\n", $styles);

            foreach ($lines as $line) {
                $lineParts = explode(':', trim($line));

                if (isset($lineParts[0], $lineParts[1])) {
                    $lineParts[0] = trim($lineParts[0]);
                    $lineParts[1] = trim($lineParts[1]);

                    $result[$lineParts[0]] = $lineParts[1];
                }
            }

            $styleGroup->setStyle($result);

            $this->getDoctrine()->getManager()->flush();

            $this->flashMessage->addSuccess(
                '',
                $this->translator->trans('flash.edit.success')
            );

            return $this->redirectToRoute('manage.geospatial.style-group.list');
        }

        $style = '';
        foreach ($styleGroup->getStyle() as $k => $v) {
            $k = trim($k);
            $v = trim($v);

            $style .= sprintf("%s: %s\n", $k, $v);
        }

        return $this->render('manage/geospatial/style/group/edit.html.twig', [
            'form' => $form->createView(),
            'style' => $style,
            'styleGroup' => $styleGroup
        ]);
    }
}
