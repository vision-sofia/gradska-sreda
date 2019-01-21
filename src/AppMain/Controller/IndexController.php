<?php

namespace App\AppMain\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class IndexController extends AbstractController
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("", name="app.homepage")
     */
    public function index(): Response
    {
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('SELECT id FROM x_geospatial.object_type WHERE name = ?');
        $stmt->execute(['Нерегулирано']);

        $resultTypeA = $this->findByType($stmt->fetchColumn());

        $stmt = $conn->prepare('SELECT id FROM x_geospatial.object_type WHERE name = ?');
        $stmt->execute(['Алея']);

        $resultTypeB = $this->findByType($stmt->fetchColumn());

        return $this->render('front/index/index.html.twig', [
            'items' => array_merge($resultTypeA, $resultTypeB)
        ]);
    }


    private function findByType(int $objectTypeId): array {
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('
            SELECT 
                g.uuid as id,
                st_asgeojson(m.coordinates) as geo,
                g.attributes,
                u.data,
                g.name as object_name,
                t.name as object_type
            FROM 
                x_geometry.multiline m
                    INNER JOIN
                x_geospatial.geo_object g ON m.geo_object_id = g.id
                    LEFT JOIN
                x_survey.result_user_completion u ON g.id = u.geo_object_id AND u.user_id = :user_id                
                    LEFT JOIN
                x_geospatial.object_type t ON g.object_type_id = t.id
            WHERE
                g.object_type_id = :object_type_id
            ORDER BY 
                g.id DESC
            LIMIT 3
        ');

        $userId =  $this->getUser() !== null ? $this->getUser()->getId() : null;

        $stmt->bindValue('object_type_id', $objectTypeId);
        $stmt->bindValue('user_id', $userId);
        $stmt->execute();

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
                'type' => $row['object_type'],
                'name' => $row['object_name'],
                'geometry' => json_decode($row['geo'], true),
                'data' => json_decode($row['data'], true)
            ];
        }

        return $result;
    }
}
