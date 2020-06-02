<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\Survey\GeoCollection\Collection;
use App\Services\GeoCollection\GeoCollection;
use App\Services\Geometry\Utils;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/geo-collection/", name="app.geo-collection.")
 * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
 */
class GeoCollectionController extends AbstractController
{
    protected $entityManager;
    protected $utils;
    protected $logger;
    protected $geoCollection;

    public function __construct(
        Utils $utils,
        LoggerInterface $logger,
        GeoCollection $geoCollection
    ) {
        $this->utils = $utils;
        $this->logger = $logger;
        $this->geoCollection = $geoCollection;
    }

    /**
     * @Route("create", name="create", methods="GET")
     */
    public function index(): Response
    {
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findBy([
            'user' => $this->getUser(),
        ]);

        return $this->render('front/geo-collection/index.html.twig', [
            'collections' => $collections,
        ]);
    }

    /**
     * @Route("{id}",
     *     name="view",
     *     methods="GET",
     *     requirements={
     *         "id": "[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-(8|9|a|b)[a-f0-9]{3}-[a-f0-9]{12}"
     *     }
     * )
     * @ParamConverter("collection", class="App\AppMain\Entity\Survey\GeoCollection\Collection", options={"mapping": {"id": "uuid"}})
     */
    public function view(Collection $collection): Response
    {
        //  $boundingBox = $this->geoCollection->findCollectionBoundingBox($this->getUser()->getId(), $collection->getUuid());

        return $this->render('front/geo-collection/view.html.twig', [
            'collection' => $collection,
            //  'boundingBox' => Utils::buildBboxFromDTO($boundingBox),
        ]);
    }
}
