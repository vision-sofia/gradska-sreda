<?php

namespace App\AppMain\Controller\APIFrontEnd;

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
    )
    {
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
        $zoom = (float)$request->query->get('zoom');

        $conn = $this->entityManager->getConnection();

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

        $i = 0;

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $i++;

            $attributes = json_decode($row['attributes'], true);

            $properties = [];

            if (!empty(trim($attributes['name']))) {
                $properties['name'] = $attributes['name'];
            }

            if (!empty(trim($attributes['type']))) {
                $properties['type'] = $attributes['type'];
            }

            $properties['category'] = $row['category_name'];

            $result[] = [
                'id'         => $row['id'],
                'properties' => $properties,
                'geometry'   => json_decode($row['geo'], true),
            ];
        }

        $this->logger->info($i);

        return new JsonResponse([
            'settings' => [
                'category' => $style,
            ],
            'objects'  => $result,
        ]);
    }
}
