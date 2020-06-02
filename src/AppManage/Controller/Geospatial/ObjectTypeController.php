<?php

namespace App\AppManage\Controller\Geospatial;

use App\AppMain\Entity\Geospatial\ObjectType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/geospatial/object-types", name="manage.geospatial.object-types.")
 */
class ObjectTypeController extends AbstractController
{
    /**
     * @Route("", name="list", methods="GET")
     */
    public function list(): Response
    {
        $geoObjectTypes = $this->getDoctrine()
            ->getRepository(ObjectType::class)
            ->findAll()
        ;

        return $this->render('manage/geospatial/object-type/list.html.twig', [
            'list' => $geoObjectTypes,
        ]);
    }
}
