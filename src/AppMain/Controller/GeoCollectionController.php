<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\Survey\GeoCollection\Collection;
use App\AppMain\Entity\Survey\Survey\Survey;
use App\Services\Geometry\Utils;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("create", name="create")
     */
    public function index(Request $request): Response
    {
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findBy([
            'user' => $this->getUser()
        ]);

        return $this->render('front/geo-collection/index.html.twig', [
            'collections' => $collections
        ]);
    }

    /**
     * @Route("add", name="add")
     */
    public function add(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $survey = $em->getRepository(Survey::class)->findOneBy(['isActive' => true]);

        $collection = new Collection();
        $collection->setUser($this->getUser());
        $collection->setSurvey($survey);
        $collection->onPrePersist();

        $em->persist($collection);
        $em->flush();

        return $this->redirectToRoute('app.geo-collection.view', [
            'id' => $collection->getId()
        ]);
    }

    /**
     * @Route("{id}/view", name="view")
     * @ParamConverter("collection", class="App\AppMain\Entity\Survey\GeoCollection\Collection", options={"mapping": {"id" = "uuid"}})
     */
    public function view(Request $request, Collection $collection): Response
    {
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findBy([
            'user' => $this->getUser()
        ]);



        return $this->render('front/geo-collection/index.html.twig', [
            'collections' => $collections,
            'collection' => $collection
        ]);
    }
}
