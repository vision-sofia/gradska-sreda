<?php

namespace App\AppMain\Controller\APIFrontEnd;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/map", name="api.map")
     */
    public function index(Request $request): Response
    {
        $center = $request->query->get('center');

        $distance = $request->query->get('distance');

        $conn = $this->entityManager->getConnection();

        if (null !== $center && null !== $distance) {
            [$lat, $lon] = explode(',', $center);

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
                WHERE
                    ST_Distance(coordinates, ST_MakePoint(:center_lon,:center_lat)) <= :distance
            ');

            $stmt->bindValue('center_lat', $lat);
            $stmt->bindValue('center_lon', $lon);
            $stmt->bindValue('distance', $distance);
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

        return new JsonResponse([
            'settings' => [
                'category' => $style,
            ],
            'objects'  => $result,
        ]);
    }
}
