<?php

namespace App\AppMain\Controller\APIFrontEnd;

use App\AppMain\Entity\Geospatial\Simplify;
use App\AppManage\Entity\Settings;
use App\Services\Geometry\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    protected $entityManager;
    protected $utils;
    protected $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        Utils $utils,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->utils = $utils;
        $this->logger = $logger;
    }

    /**
     * @Route("/map", name="api.map")
     */
    public function index(Request $request): Response
    {
        $in = $request->query->get('in');
        $zoom = $request->query->get('zoom');

        $conn = $this->entityManager->getConnection();

        if (null === $in || null === $zoom) {
            return new JsonResponse(['Missing parameters'], 400);
        }

        $simplify = $this->getDoctrine()->getRepository(Simplify::class)->findAll();

        $simplifyRanges = [];
        foreach ($simplify as $item) {
            $simplifyRanges[] = [
                'min_zoom' => $item->getMinZoom(),
                'max_zoom' => $item->getMaxZoom(),
                'tolerance' => $item->getTolerance(),
            ];
        }

        $stmt = $conn->prepare('
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
            SELECT
                g.id,
                g.uuid,
                g.name as geo_name,
                t.name as type_name,
                g.attributes,
                g.geometry,
                gc.geo_object_id as entry
            FROM
                g
                    INNER JOIN
                x_geospatial.object_type t ON t.id = g.object_type_id
                    LEFT JOIN 
                x_survey.gc_collection_content gc 
                    LEFT JOIN
                x_survey.gc_collection c 
                    ON gc.geo_collection_id = c.id 
                    ON gc.geo_object_id = g.id 
                    AND c.user_id = :user_id
                    AND c.uuid = :collection_id                
        ');

        $zoom = (float) $zoom;
        $simplifyTolerance = $this->utils->findTolerance($simplifyRanges, $zoom);
        $collectionId = $request->query->get('collection');

        $stmt->bindValue('x_min', $this->utils->bbox($in, 0));
        $stmt->bindValue('y_min', $this->utils->bbox($in, 1));
        $stmt->bindValue('x_max', $this->utils->bbox($in, 2));
        $stmt->bindValue('y_max', $this->utils->bbox($in, 3));
        $stmt->bindValue('zoom', $zoom);
        $stmt->bindValue('simplify_tolerance', $simplifyTolerance);
        $stmt->bindValue('collection_id', $collectionId);

        if ($this->getUser()) {
            $stmt->bindValue('user_id', $this->getUser()->getId());
        } else {
            $stmt->bindValue('user_id', null);
        }
        $stmt->execute();

        $styles = $this->getDoctrine()->getRepository(Settings::class)->findOneBy(['key' => 'map_style']);
        $styles = json_decode($styles->getValue());

        $i = 0;

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            ++$i;

            $geometry = json_decode($row['geometry'], true);
            $attributes = json_decode($row['attributes'], true);

            if (isset($attributes['_sca']) && 'Пешеходни отсечки' === $attributes['_sca']) {
                $s1 = 'cat1';
                $s2 = 'line_hover';
                $attributes['_dtext'] = 2;
            } elseif (isset($attributes['_sca']) && 'Алеи' === $attributes['_sca']) {
                $s1 = 'cat2';
                $s2 = 'line_hover';
                $attributes['_dtext'] = 3;
            } elseif (isset($attributes['_sca']) && 'Пресичания' === $attributes['_sca']) {
                $s1 = 'cat3';
                $s2 = 'line_hover';
                $attributes['_dtext'] = 1;
            } elseif ('MultiLineString' === $geometry['type']) {
                $s1 = 'line_main';
                $s2 = 'line_hover';
            } elseif ('Polygon' === $geometry['type']) {
                $s1 = 'poly_main';
                $s2 = 'poly_hover';
            } elseif ('Point' === $geometry['type']) {
                $s1 = 'point_default';
                $s2 = 'point_hover';
            } else {
                $s1 = '';
                $s2 = '';
            }

            if (null !== $row['entry']) {
                $s1 = 'm';
            }

            if($row['type_name'] === 'Градоустройствена единица') {
                $attributes['_zoom'] = 17;
            }

            if(isset($attributes['_sca']) && $attributes['_sca'] === 'Пресичания') {
                $attributes['_zoom'] = 20;
            }

            $result[] = [
                'type' => 'Feature',
                'geometry' => $geometry,
                'properties' => [
                    '_s1' => $s1,
                    '_s2' => $s2,
                    'id' => $row['uuid'],
                    'name' => $row['geo_name'],
                    'type' => $row['type_name'],
                ] + $attributes,
            ];
        }

        $this->logger->info('Map view', [
            'zoom' => $zoom,
            'bbox' => $in,
            'simplify_tolerance' => $simplifyTolerance,
            'objects' => $i,
        ]);

        return new JsonResponse([
            'settings' => [
                'default_zoom' => 17,
                'styles' => $styles,
                'dialog' => [
                    1 => 'Искате ли да оцените избраното пресичане',
                    2 => 'Искате ли да оцените избрания тротоар',
                    3 => 'Искате ли да оцените избраната алея',
                ]
            ],
            'objects' => $result,
        ]);
    }
}
