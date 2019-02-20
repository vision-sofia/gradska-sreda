<?php

namespace App\AppMain\Controller\APIFrontEnd;

use App\AppMain\Entity\Geospatial\Simplify;
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
                            AND ST_Intersects(m.coordinates, ST_MakePolygon(ST_GeomFromText(:linestring, 4326))) = TRUE
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
                            ST_Intersects(m.coordinates, ST_MakePolygon(ST_GeomFromText(:linestring, 4326))) = TRUE
                            AND :zoom <= min_zoom AND :zoom > max_zoom
                    ) as w
            )
            SELECT
                g.id,
                g.uuid,
                g.name as geo_name,
                t.name as type_name,
                g.attributes,
                g.geometry
            FROM
                g
                    INNER JOIN
                x_geospatial.object_type t ON t.id = g.object_type_id
        ');

        $zoom = (float) $zoom;
        $simplifyTolerance = $this->utils->findTolerance($simplifyRanges, $zoom);

        $stmt->bindValue('linestring', sprintf('LINESTRING(%s)', $this->utils->parseCoordinates($in)));
        $stmt->bindValue('zoom', $zoom);
        $stmt->bindValue('simplify_tolerance', $simplifyTolerance);
        $stmt->execute();

        $styles = [
            'cat1' => [
                'color' => '#0099ff',
                'opacity' => 0.5,
                'width' => 5,
            ],
            'cat2' => [
                'color' => '#33cc33',
                'opacity' => 0.5,
                'weight' => 5,
            ],
            'cat3' => [
                'color' => '#ff3300',
                'opacity' => 0.5,
                'weight' => 5,
            ],
            'poly' => [
                'stroke' => '#ff3300',
                'strokeWidth' => 5,
                'strokeOpacity' => 0.2,
                'fill' => '#ff00ff',
                'fillOpacity' => 0.5,
            ],
            'line_main' => [
                'color' => '#ff99ff',
                'opacity' => 0.5,
                'width' => 3,
            ],
            'line_hover' => [
                'opacity' => 0.8,
            ],
            'point_default' => [
                'radius' => 8,
                'fillColor' => '#ff7800',
                'color' => '#000',
                'weight' => 1,
                'opacity' => 1,
                'fillOpacity' => 0.8,
            ],
            'point_hover' => [
                'fillColor' => '#ff00ff',
            ],
            'poly_main' => [
                'stroke' => '#ff99ff',
                'strokeWidth' => 1,
                'strokeOpacity' => 0.2,
                'fill' => '#ff00ff',
                'fillOpacity' => 0.5,
            ],
             'poly_hover' => [
                'fillOpacity' => 0.8,
            ],
        ];

        $i = 0;

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            ++$i;

            $geometry = json_decode($row['geometry'], true);
            $attributes = json_decode($row['attributes'], true);

            if (isset($attributes['_sca']) && 'Пешеходни отсечки' === $attributes['_sca']) {
                $s1 = 'cat1';
                $s2 = 'line_hover';
            } elseif (isset($attributes['_sca']) && 'Алеи' === $attributes['_sca']) {
                $s1 = 'cat2';
                $s2 = 'line_hover';
            } elseif (isset($attributes['_sca']) && 'Пресичания' === $attributes['_sca']) {
                $s1 = 'cat3';
                $s2 = 'line_hover';
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

            if($row['type_name'] === 'Градоустройствена единица') {
                $attributes['_zoom'] = 17;
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
            'simplify_tolerance' => $simplifyTolerance,
            'objects' => $i,
        ]);

        return new JsonResponse([
            'settings' => [
                'styles' => $styles,
            ],
            'objects' => $result,
        ]);
    }
}
