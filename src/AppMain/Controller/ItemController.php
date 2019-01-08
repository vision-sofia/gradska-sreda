<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\Geospatial\GeospatialObject;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemController extends AbstractController
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("geo/{id}", name="app.geospatial_object.details")
     * @ParamConverter("geospatialObject", class="App\AppMain\Entity\Geospatial\GeospatialObject", options={"mapping": {"id" = "uuid"}})
     */
    public function details(GeospatialObject $geospatialObject): Response
    {
        $type = $geospatialObject->getAttributes()['type'];





        return $this->render('front/geo-object/details.html.twig', [
            'geo_object' => $geospatialObject,
        ]);
    }
}
