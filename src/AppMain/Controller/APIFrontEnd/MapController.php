<?php

namespace App\AppMain\Controller\APIFrontEnd;

use App\Services\Geometry\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    protected $entityManager;
    protected $utils;

    public function __construct(EntityManagerInterface $entityManager, Utils $utils)
    {
        $this->entityManager = $entityManager;
        $this->utils = $utils;
    }

    /**
     * @Route("/map", name="api.map")
     */
    public function index(Request $request): Response
    {
        $in = $request->query->get('in');
        $zoom = (float)$request->query->get('zoom');

        $conn = $this->entityManager->getConnection();

        /*

        SELECT
            g.uuid AS id,
            st_asgeojson(m.coordinates) AS coordinates,
            jsonb_build_object(
                '_sct', c.name,
                'name', g.attributes->'name',
                'type', g.attributes->'type'
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
            AND ST_Intersects(m.coordinates, ST_MakePolygon(ST_GeomFromText('LINESTRING(23.23743581771851 42.71862621934375, 23.23743581771851 42.70672263493815, 23.271446228027347 42.70672263493815, 23.271446228027347 42.71862621934375, 23.23743581771851 42.71862621934375)', 4326))) = TRUE
            AND 16 BETWEEN max_zoom AND min_zoom


        ;

        EXPLAIN ANALYSE SELECT
            g.uuid AS id,
            st_asgeojson(m.coordinates) AS coordinates,
            jsonb_build_object(
                '_au', 1,
                'name', g.attributes->'name',
                'type', g.attributes->'type'
            ) as attributes
        FROM
            x_geometry.geometry_base m
                INNER JOIN
            x_geospatial.geo_object g ON m.geo_object_id = g.id
                INNER JOIN
            x_survey.survey_auxiliary_object_type a ON g.object_type_id = a.object_type_id
                INNER JOIN
            x_survey.survey s ON a.survey_id = s.id
                INNER JOIN
            x_geospatial.object_type_visibility v ON g.object_type_id = v.object_type_id
        WHERE
            s.is_active = TRUE
            AND ST_Intersects(m.coordinates, ST_MakePolygon(ST_GeomFromText('LINESTRING(13.24841141700745 12.71148040100946 , 23.24841141700745 42.70850437629568 , 23.256994485855103 42.70850437629568 , 23.256994485855103 42.71148040100946 , 13.24841141700745 12.71148040100946)', 4326))) = TRUE
            AND 16 BETWEEN max_zoom AND min_zoom


         */

        if (null !== $in) {
            $stmt = $conn->prepare('
                SELECT 
                    g.uuid AS id,
                    st_asgeojson(m.coordinates) AS geo,
                    g.attributes,
                    c.name AS category_name
                FROM 
                    x_geometry.multiline m
                        INNER JOIN
                    x_geospatial.geo_object g ON m.geo_object_id = g.id
                        INNER JOIN 
                    x_geospatial.object_type t ON g.object_type_id = t.id
                        INNER JOIN
                    x_survey.survey_element e ON g.object_type_id = e.object_type_id
                        INNER JOIN
                    x_survey.survey_category c ON e.category_id = c.id
                        INNER JOIN
                    x_geospatial.object_type_visibility v ON t.id = v.object_type_id
                WHERE
                    ST_Intersects(m.coordinates, ST_MakePolygon(ST_GeomFromText(:text, 4326))) = TRUE
                    AND :zoom BETWEEN max_zoom AND min_zoom
            ');

            $stmt->bindValue('text', sprintf('LINESTRING(%s)', $this->utils->parseCoordinates($in)));
            $stmt->bindValue('zoom', $zoom);
            $stmt->execute();

        } else {
            $stmt = $conn->prepare('
                SELECT 
                    g.uuid AS id,
                    st_asgeojson(m.coordinates) AS geo,
                    g.attributes,
                    c.name AS category_name
                FROM 
                    x_geometry.multiline m
                        INNER JOIN
                    x_geospatial.geo_object g ON m.geo_object_id = g.id
                        INNER JOIN 
                    x_geospatial.object_type t ON g.object_type_id = t.id
                        INNER JOIN
                    x_survey.survey_element e ON g.object_type_id = e.object_type_id
                        INNER JOIN
                    x_survey.survey_category c ON e.category_id = c.id
                
            ');

            $stmt->execute();
        }


        $style = [
            [
                'name'  => 'Пешеходни отсечки',
                'style' => [
                    'stroke' => [
                        'color'   => '#0099ff',
                        'opacity' => 0.5,
                        'width'   => 5,
                    ],
                ],
            ],
            [
                'name'  => 'Алеи',
                'style' => [
                    'stroke' => [
                        'color'   => '#33cc33',
                        'opacity' => 0.5,
                        'width'   => 5,
                    ],
                ],
            ],
            [
                'name'  => 'Пресичания',
                'style' => [
                    'stroke' => [
                        'color'   => '#ff3300',
                        'opacity' => 0.5,
                        'width'   => 5,
                    ],
                ],
            ],
        ];

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $attributes = json_decode($row['attributes'], true);

            if (empty(trim($attributes['name']))) {
                unset($attributes['name']);
            }

            if (empty(trim($attributes['type']))) {
                unset($attributes['type']);
            }

            $result[] = [
                'id'         => $row['id'],
                'attributes' => $attributes,
                'geometry'   => json_decode($row['geo'], true),
            ];
        }

        return new JsonResponse([
            'settings' => [
                'category' => $style,
            ],
            'objects'  => $result,
        ]);
    }
}



