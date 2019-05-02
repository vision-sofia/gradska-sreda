<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\Survey\GeoCollection\Collection;
use App\AppMain\Entity\Survey\Survey\Survey;
use App\Services\GeoCollection\GeoCollection;
use App\Services\Geometry\Utils;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function index(GeoCollection $geoCollection): Response
    {
        $boundingBox = $geoCollection->findCollectionBoundingBoxByUser($this->getUser()->getId());

        $collections = $this->getDoctrine()->getRepository(Collection::class)->findBy([
            'user' => $this->getUser(),
        ]);

        return $this->render('front/geo-collection/index.html.twig', [
            'collections' => $collections,
            'boundingBox' => '',
        ]);
    }

    /**
     * @Route("add", name="add", methods="POST")
     */
    public function add(): Response
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Survey $survey */
        $survey = $em->getRepository(Survey::class)->findOneBy(['isActive' => true]);

        $collection = new Collection();
        $collection->setUser($this->getUser());
        $collection->setSurvey($survey);
        $collection->onPrePersist();

        $em->persist($collection);
        $em->flush();

        return $this->redirectToRoute('app.geo-collection.view', [
            'id' => $collection->getUuid(),
        ]);
    }

    /**
     * @Route("{id}", name="view", methods="GET")
     * @ParamConverter("collection", class="App\AppMain\Entity\Survey\GeoCollection\Collection", options={"mapping": {"id" = "uuid"}})
     */
    public function view(Collection $collection): Response
    {
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findBy([
            'user' => $this->getUser(),
        ]);

        $boundingBox = $this->geoCollection->findCollectionBoundingBox($this->getUser()->getId(), $collection->getUuid());

        $completion = $this->geoCollection->findCompletion($collection->getId());
        $length = $this->geoCollection->findLength($collection->getId());

        return $this->render('front/geo-collection/view.html.twig', [
            'collections' => $collections,
            'collection' => $collection,
            'stats' => $completion + ['length' => $length],
            'boundingBox' => Utils::buildBboxFromDTO($boundingBox),
        ]);
    }

    /**
     * @Route("{id}", name="delete", methods="DELETE")
     * @ParamConverter("collection", class="App\AppMain\Entity\Survey\GeoCollection\Collection", options={"mapping": {"id" = "uuid"}})
     */
    public function delete(Collection $collection): Response
    {
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findBy([
            'user' => $this->getUser(),
        ]);

        $boundingBox = $this->geoCollection->findCollectionBoundingBox($this->getUser()->getId(), $collection->getUuid());

        $completion = $this->geoCollection->findCompletion($collection->getId());
        $length = $this->geoCollection->findLength($collection->getId());

        return $this->render('front/geo-collection/view.html.twig', [
            'collections' => $collections,
            'collection' => $collection,
            'stats' => $completion + ['length' => $length],
            'boundingBox' => Utils::buildBboxFromDTO($boundingBox),
        ]);
    }

    /**
     * @Route("{id}/info", name="info", methods="GET")
     * @ParamConverter("collection", class="App\AppMain\Entity\Survey\GeoCollection\Collection", options={"mapping": {"id" = "uuid"}})
     */
    public function info(Collection $collection): Response
    {
        /** @var Collection[] $collections */
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findBy([
            'user' => $this->getUser(),
        ]);

        $result = [];

        foreach ($collections as $item) {
            $completion = $this->geoCollection->findCompletion($item->getId());
            $length = $this->geoCollection->findLength($item->getId());

            $result[] = [
                'collection_uuid' => $item->getUuid(),
                'length' => $length,
                'completion' => $completion
            ];
        }

       # return new JsonResponse($completion + ['length' => $length]);
        return new JsonResponse($result);
        #return new Response('asd');
    }
}
