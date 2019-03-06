<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\Survey\GeoCollection\Collection;
use App\AppMain\Entity\Survey\Survey\Survey;
use App\Services\Geometry\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
            'user' => $this->getUser(),
        ]);


        return $this->render('front/geo-collection/index.html.twig', [
            'collections' => $collections,
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
            'id' => $collection->getUuid(),
        ]);
    }

    /**
     * @Route("{id}", name="view")
     * @ParamConverter("collection", class="App\AppMain\Entity\Survey\GeoCollection\Collection", options={"mapping": {"id" = "uuid"}})
     */
    public function view(Request $request, Collection $collection, EntityManagerInterface $entityManager): Response
    {
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findBy([
            'user' => $this->getUser(),
        ]);

        return $this->render('front/geo-collection/index.html.twig', [
            'collections' => $collections,
            'collection' => $collection,
        ]);
    }
}
