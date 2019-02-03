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
        $zoom = $request->query->get('zoom');

        $conn = $this->entityManager->getConnection();

        if (null === $in || $zoom === null) {
            return new JsonResponse(['Missing parameters'],400);
        }

        $stmt = $conn->prepare('
            SELECT
                id,
                geometry,
                name,
                jsonb_strip_nulls(attributes) as attributes
            FROM
                 (
                    SELECT
                        g.uuid AS id,
                        st_asgeojson(m.coordinates) AS geometry,
                        g.name as name,
                        jsonb_build_object(
                            \'_sca\', c.name,
                            \'behavior\', \'survey\'
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
                        AND :zoom BETWEEN max_zoom AND min_zoom
            
                    UNION ALL
            
                    SELECT
                        g.uuid AS id,
                        st_asgeojson(m.coordinates) AS geometry,
                        g.name as name,
                        jsonb_build_object(
                            \'behavior\', a.behavior
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
                        AND :zoom BETWEEN max_zoom AND min_zoom
                 ) as w               
            ');

        $stmt->bindValue('linestring', sprintf('LINESTRING(%s)', $this->utils->parseCoordinates($in)));
        $stmt->bindValue('zoom', (int)$zoom);
        $stmt->execute();

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
            $result[] = [
                'id'         => $row['id'],
                'attributes' => json_decode($row['attributes'], true),
                'geometry'   => json_decode($row['geometry'], true),
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



