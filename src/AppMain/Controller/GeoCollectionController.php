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
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    )
    {
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
     * @Route("add", name="add", methods="POST")
     */
    public function add(): Response
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Survey $survey */
        $survey = $em
            ->getRepository(Survey::class)
            ->findOneBy([
                'isActive' => true
            ]);

        $collection = new Collection();
        $collection->setUser($this->getUser());
        $collection->setSurvey($survey);

        $em->persist($collection);
        $em->flush();

        return $this->redirectToRoute('app.geo-collection.view', [
            'id' => $collection->getUuid(),
        ]);
    }

    /**
     * @Route("{id}",
     *     name="view",
     *     methods="GET",
     *     requirements={
     *         "id"="[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-(8|9|a|b)[a-f0-9]{3}-[a-f0-9]{12}"
     *     }
     * )
     * @ParamConverter("collection", class="App\AppMain\Entity\Survey\GeoCollection\Collection", options={"mapping": {"id" = "uuid"}})
     */
    public function view(Collection $collection): Response
    {
        //  $boundingBox = $this->geoCollection->findCollectionBoundingBox($this->getUser()->getId(), $collection->getUuid());

        return $this->render('front/geo-collection/view.html.twig', [
            'collection' => $collection,
            //  'boundingBox' => Utils::buildBboxFromDTO($boundingBox),
        ]);
    }

    /**
     * @Route("{id}",
     *     name="delete",
     *     methods="DELETE",
     *     requirements={
     *         "id"="[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-(8|9|a|b)[a-f0-9]{3}-[a-f0-9]{12}"
     *     }
     * )
     * @ParamConverter("collection", class="App\AppMain\Entity\Survey\GeoCollection\Collection", options={"mapping": {"id" = "uuid"}})
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

        return new JsonResponse([], 302);
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

            $result[] = [
                'collectionUuid' => $item->getUuid(),
                'length' => $length,
                'completion' => $completion,
                'interconnectedClustersCount' => $this->geoCollection->countInterconnectedClusters($item->getUuid())
            ];
        }

        return new JsonResponse($result);
    }
}
