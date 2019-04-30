<?php

namespace App\AppManage\Controller\Geospatial;

use App\AppMain\Entity\Geospatial\ObjectTypeVisibility;
use App\AppManage\Form\Type\ObjectTypeVisibilityType;
use App\Services\FlashMessage\FlashMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/geospatial/object-type-visibility", name="manage.geospatial.object-type-visibility.")
 */
class ObjectTypeVisibilityController extends AbstractController
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
            ->getRepository(ObjectTypeVisibility::class)
            ->findBy([], [
                'zoom' => 'DESC'
            ]);

        return $this->render('manage/geospatial/visibility/list.html.twig', [
            'list' => $visibilities,
        ]);
    }

    /**
     * @Route("/{id}", name="edit", methods={"GET", "POST"})
     * @ParamConverter("objectTypeVisibility", class="App\AppMain\Entity\Geospatial\ObjectTypeVisibility", options={"mapping": {"id": "uuid"}})
     */
    public function edit(Request $request, ObjectTypeVisibility $objectTypeVisibility): Response
    {
        $form = $this->createForm(ObjectTypeVisibilityType::class, $objectTypeVisibility);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->flashMessage->addSuccess(
                '',
                $this->translator->trans('flash.edit.success')
            );

            return $this->redirectToRoute('manage.geospatial.object-type-visibility.list');
        }

        return $this->render('manage/geospatial/visibility/edit.html.twig', [
            'visibility' => $objectTypeVisibility,
            'form' => $form->createView(),
        ]);
    }
}
