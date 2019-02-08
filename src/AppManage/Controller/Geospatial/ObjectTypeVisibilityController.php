<?php

namespace App\AppManage\Controller\Geospatial;

use App\AppMain\Entity\Geospatial\ObjectType;
use App\AppMain\Entity\Geospatial\ObjectTypeVisibility;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/geospatial/object-type-visibility", name="manage.geospatial.object-type-visibility.")
 */
class ObjectTypeVisibilityController extends AbstractController
{
    /**
     * @Route("", name="list", methods="GET")
     */
    public function list(): Response
    {
        $visibilities = $this->getDoctrine()
            ->getRepository(ObjectTypeVisibility::class)
            ->findAll();

        return $this->render('manage/geospatial/visibility/list.html.twig', [
            'list' => $visibilities,
        ]);
    }
}
