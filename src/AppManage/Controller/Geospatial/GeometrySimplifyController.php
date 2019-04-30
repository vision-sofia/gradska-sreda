<?php

namespace App\AppManage\Controller\Geospatial;

use App\AppMain\Entity\Geospatial\Simplify;
use App\AppManage\Form\Type\GeometrySimplifyType;
use App\Services\FlashMessage\FlashMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/geospatial/simplify", name="manage.geospatial.simplify.")
 */
class GeometrySimplifyController extends AbstractController
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
        $visibilities = $this->getDoctrine()
            ->getRepository(Simplify::class)
            ->findBy([], [
                'zoom' => 'DESC'
            ]);

        return $this->render('manage/geospatial/geometry-simplify/list.html.twig', [
            'list' => $visibilities,
        ]);
    }

    /**
     * @Route("/{id}", name="edit", methods={"GET", "POST"})
     * @ParamConverter("simplify", class="App\AppMain\Entity\Geospatial\Simplify", options={"mapping": {"id": "uuid"}})
     */
    public function edit(Request $request, Simplify $simplify): Response
    {
        $form = $this->createForm(GeometrySimplifyType::class, $simplify);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->flashMessage->addSuccess(
                '',
                $this->translator->trans('flash.edit.success')
            );

            return $this->redirectToRoute('manage.geospatial.simplify.list');
        }

        return $this->render('manage/geospatial/geometry-simplify/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
