<?php

namespace App\AppMain\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/network", name="api.network")
     */
    public function index(Request $request): Response
    {
        $center = $request->query->get('center');

        $distance = $request->query->get('distance');

        $conn = $this->entityManager->getConnection();

        if($center !== null && $distance !== null) {
            [$lat, $lon] = explode(',', $center);

            $stmt = $conn->prepare('
                SELECT 
                    g.uuid as id,
                    ST_AsGeoJSON(m.coordinates) as geo,
                    g.attributes 
                FROM 
                    x_geometry.multiline m
                        INNER JOIN
                    x_geospatial.geo_object g ON m.spatial_object_id = g.id
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
                    g.uuid as id,
                    st_asgeojson(m.coordinates) as geo,
                    g.attributes 
                FROM 
                    x_geometry.multiline m
                        INNER JOIN
                    x_geospatial.geo_object g ON m.geo_object_id = g.id
            ');

            $stmt->execute();
        }

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $attributes = json_decode($row['attributes'], true);

            $properties = [];

            if(!empty(trim($attributes['name']))) {
                $properties['name'] = $attributes['name'];
            }

            if(!empty(trim($attributes['type']))) {
                $properties['type'] = $attributes['type'];
            }

            $result[] = [
                'id' => $row['id'],
                'properties' => $properties,
                'geometry' => json_decode($row['geo'], true)
            ];
        }

        return new JsonResponse($result);
    }
}
