<?php

namespace App\AppMain\Controller\APIFrontEnd;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey\GeoCollection\Collection;
use App\AppMain\Entity\Survey\GeoCollection\Entry;
use App\AppMain\Entity\Survey\Survey\Survey;
use App\Services\Geometry\Utils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/geo-collection/", name="app.geo-collection.")
 */
class GeoCollectionController extends AbstractController
{
    protected $entityManager;
    protected $utils;
    protected $logger;

    public function __construct(
        Utils $utils,
        LoggerInterface $logger
    ) {
        $this->utils = $utils;
        $this->logger = $logger;
    }

    /**
     * @Route("add", name="add")
     */
    public function index(Request $request): JsonResponse
    {
        if (null === $this->getUser()) {
            return new JsonResponse([], 200);
        }

        $em = $this->getDoctrine()->getManager();

        $collection = $em->getRepository(Collection::class)->findOneBy([
            'user' => $this->getUser(),
        ])
        ;

        if (null === $collection) {
            $survey = $em->getRepository(Survey::class)->findOneBy(['isActive' => true]);

            $collection = new Collection();
            $collection->setUser($this->getUser());
            $collection->setSurvey($survey);
            $collection->onPrePersist();

            $em->persist($collection);
            $em->flush();
        }

        $geoObjectId = $request->request->get('geo-object');

        $geoObject = $em->getRepository(GeoObject::class)->findOneBy(['uuid' => $geoObjectId]);

        $entry = $em->getRepository(Entry::class)->findOneBy([
            'collection' => $collection,
            'geoObject' => $geoObject,
        ]);

        if ($entry) {
            $em->remove($entry);
            $em->flush();
        } else {
            $content = new Entry();
            $content->setGeoObject($geoObject);
            $content->setCollection($collection);

            $em->persist($content);
            $em->flush();
        }

        return new JsonResponse([], 200);
    }
}
