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

        $with = '
            WITH g AS (
                SELECT
                    id,
                    uuid,
                    name,
                    object_type_id,
                    geometry,
                    jsonb_strip_nulls(attributes) as attributes
                FROM
                    (
                        SELECT
                            g.id,
                            g.uuid,
                            g.name,
                            g.object_type_id,
                            st_asgeojson(ST_Simplify(m.coordinates::geometry, :simplify_tolerance, true)) AS geometry,
                            jsonb_build_object(
                                \'_sca\', c.name,
                                \'_behavior\', \'survey\'
                            ) as attributes
                        FROM
                            x_geometry.geometry_base m
                                INNER JOIN
                            x_geospatial.geo_object g ON m.geo_object_id = g.id
                                INNER JOIN
                            x_survey.survey_element e ON g.object_type_id = e.object_type_id
                                INNER JOIN
                            x_survey.survey_category c ON e.category_id = c.id
                                INNER JOIN
                            x_geospatial.object_type_visibility v ON g.object_type_id = v.object_type_id
                                INNER JOIN
                            x_survey.survey s ON c.survey_id = s.id
                        WHERE
                            s.is_active = TRUE
                            AND m.coordinates && ST_MakeEnvelope(:x_min, :y_min, :x_max, :y_max)
                            AND :zoom <= min_zoom AND :zoom > max_zoom

                        UNION ALL

                        SELECT
                            g.id,
                            g.uuid,
                            g.name,
                            g.object_type_id,
                            st_asgeojson(ST_Simplify(m.coordinates::geometry, :simplify_tolerance, true)) AS geometry,
                            jsonb_build_object(
                                \'_behavior\', a.behavior
                            ) as attributes
                        FROM
                            x_geometry.geometry_base m
                                INNER JOIN
                            x_geospatial.geo_object g ON m.geo_object_id = g.id
                                INNER JOIN
                            x_survey.survey_auxiliary_object_type a ON g.object_type_id = a.object_type_id
                                LEFT JOIN
                            x_survey.survey s ON a.survey_id = s.id AND s.is_active = TRUE
                                INNER JOIN
                            x_geospatial.object_type_visibility v ON g.object_type_id = v.object_type_id
                        WHERE
                            m.coordinates && ST_MakeEnvelope(:x_min, :y_min, :x_max, :y_max)
                            AND :zoom <= min_zoom AND :zoom > max_zoom
                    ) as w
            )        
        ';

        $qb = $entityManager->getConnection()->createQueryBuilder();


        $qb->select([
            'g.id',

        ]);
        $qb->from('g');
        $qb->innerJoin('g', 'x_geospatial.object_type', 't', 't.id = g.object_type_id');
        $qb->leftJoin('t', '(x_survey.gc_collection_content gc
        INNER JOIN
    x_survey.gc_collection c ON (gc.geo_collection_id = c.id AND c.user_id = 19))', '', 'gc.geo_object_id = g.id');


        dump( $qb->getSQL());

        $collections = $this->getDoctrine()->getRepository(Collection::class)->findBy([
            'user' => $this->getUser(),
        ]);

        return $this->render('front/geo-collection/index.html.twig', [
            'collections' => $collections,
            'collection' => $collection,
        ]);
    }
}
