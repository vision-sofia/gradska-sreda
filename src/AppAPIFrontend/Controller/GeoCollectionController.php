<?php

namespace App\AppAPIFrontend\Controller;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey\GeoCollection\Collection;
use App\AppMain\Entity\Survey\GeoCollection\Entry;
use App\Services\GeoCollection\GeoCollection;
use App\Services\Geometry\Utils;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
 * @Route("/geo-collection/", name="api.geo-collection.")
 */
class GeoCollectionController extends AbstractController
{
    protected $entityManager;
    protected $geoCollection;
    protected $utils;
    protected $logger;

    public function __construct(
        GeoCollection $geoCollection,
        Utils $utils,
        LoggerInterface $logger
    ) {
        $this->utils = $utils;
        $this->logger = $logger;
        $this->geoCollection = $geoCollection;
    }

    /**
     * @Route("add", name="add", methods={"POST"})
     */
    public function index(Request $request): JsonResponse
    {
        $geoObjectId = $request->request->get('geo-object');
        $collectionId = $request->request->get('collection');

        $em = $this->getDoctrine()->getManager();

        /** @var Collection $collection */
        $collection = $em->getRepository(Collection::class)
            ->findOneBy([
                'user' => $this->getUser(),
                'uuid' => $collectionId,
            ])
        ;

        /** @var GeoObject $geoObject */
        $geoObject = $em->getRepository(GeoObject::class)
            ->findOneBy([
                'uuid' => $geoObjectId,
            ])
        ;

        $entry = $em->getRepository(Entry::class)->findOneBy([
            'collection' => $collection,
            'geoObject' => $geoObject,
        ]);

        if ($entry) {
            $em->remove($entry);
            $em->flush();

            return new JsonResponse([], 200);
        }

        if($collection->getEntries()->isEmpty()
            || $this->geoCollection->isTouchingGeoCollection(
            $collection->getUuid(),
            $this->getUser()->getId(),
            $geoObject->getUuid()
        )) {
            $content = new Entry();
            $content->setGeoObject($geoObject);
            $content->setCollection($collection);

            $em->persist($content);
            $em->flush();

            return new JsonResponse([], 200);
        }

        return new JsonResponse(['error' => 'notTouchingGeoCollection'], 200);
    }
}
