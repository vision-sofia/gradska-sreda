<?php

namespace App\AppAPIFrontend\Controller;

use App\AppMain\Entity\Geospatial\GeoObject;
use App\AppMain\Entity\Survey\GeoCollection\Collection;
use App\AppMain\Entity\Survey\GeoCollection\Entry;
use App\AppMain\Entity\Survey\Survey\Survey;
use App\Services\GeoCollection\GeoCollection;
use App\Services\Geometry\Utils;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function add(Request $request): JsonResponse
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

            $this->geoCollection->updateBBoxGeometry($collection->getId());
            $this->geoCollection->updateBBoxMetadata($collection->getId());

            return new JsonResponse([], 200);
        }

        if ($collection->getEntries()->isEmpty()
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

            $this->geoCollection->updateBBoxGeometry($collection->getId());
            $this->geoCollection->updateBBoxMetadata($collection->getId());

            return new JsonResponse([], 200);
        }

        return new JsonResponse(['error' => 'notTouchingGeoCollection'], 200);
    }

    /**
     * @Route("new", name="new", methods="POST")
     */
    public function new(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Survey $survey */
        $survey = $em
            ->getRepository(Survey::class)
            ->findOneBy([
                'isActive' => true,
            ])
        ;

        $name = (string) $request->request->get('name');

        $collection = new Collection();
        $collection->setUser($this->getUser());
        $collection->setSurvey($survey);
        $collection->setName($name);

        $em->persist($collection);
        $em->flush();

        return new JsonResponse([
            'id' => $collection->getUuid(),
        ]);
    }

    /**
     * @Route("info", name="info", methods="GET")
     */
    public function info(): Response
    {
        /** @var Collection[] $collections */
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findBy([
            'user' => $this->getUser(),
        ]);

        $result = [];

        foreach ($collections as $item) {
            // TODO: cache metadata on collection change
            $completion = $this->geoCollection->findCompletion($item->getId());
            $length = $this->geoCollection->findLength($item->getId());

            $metadata = $item->getBboxMetadata();

            /*            if ($metadata) {
                            $bbox = [
                                'center' => [
                                    'lat' => $metadata['y_center'] ?? null,
                                    'lng' => $metadata['x_center'] ?? null,
                                ],
                                'bounds' => [
                                    [$metadata['x_min'], $metadata['y_min']],
                                    [$metadata['x_max'], $metadata['y_max']]
                                ],
                            ];
                        }*/

            $result[] = [
                'id' => $item->getUuid(),
                'identify' => $item->getId(),
                'length' => $length,
                'completion' => $completion,
                'name' => $item->getName(),
                // 'bbox' => $bbox ?? null,
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("{id}",
     *     name="edit",
     *     methods="POST",
     *     requirements={
     *         "id": "[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-(8|9|a|b)[a-f0-9]{3}-[a-f0-9]{12}"
     *     }
     * )
     * @ParamConverter("collection", class="App\AppMain\Entity\Survey\GeoCollection\Collection", options={"mapping": {"id": "uuid"}})
     */
    public function edit(Request $request, Collection $collection): JsonResponse
    {
        // TODO: improve this
        // TODO: csrf check
        if ($collection->getUser() === $this->getUser()) {
            $name = $request->request->get('name');

            $collection->setName($name);

            $this->getDoctrine()->getManager()->flush();
        }

        return new JsonResponse([]);
    }

    /**
     * @Route("{id}",
     *     name="delete",
     *     methods="DELETE",
     *     requirements={
     *         "id": "[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-(8|9|a|b)[a-f0-9]{3}-[a-f0-9]{12}"
     *     }
     * )
     * @ParamConverter("collection", class="App\AppMain\Entity\Survey\GeoCollection\Collection", options={"mapping": {"id": "uuid"}})
     */
    public function delete(Collection $collection): JsonResponse
    {
        // TODO: improve this
        // TODO: csrf check
        if ($collection->getUser() === $this->getUser()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($collection);
            $em->flush();
        }

        return new JsonResponse([]);
    }
}
