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
     * @Route("", name="app.index")
     */
    public function index(): Response
    {
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('
            SELECT 
                g.uuid as id,
                st_asgeojson(m.coordinates) as geo,
                g.attributes,
                u.data
            FROM 
                x_geometry.multiline m
                    INNER JOIN
                x_geospatial.geospatial_object g ON m.spatial_object_id = g.id
                    LEFT JOIN
                x_survey.result_user_completion u ON g.id = u.geo_object_id AND u.user_id = :user_id                
            WHERE
                g.attributes->>\'type\' = \'Тротоар\' 
            LIMIT 3
        ');

        if($this->getUser()) {
            $userId =  $this->getUser()->getId();
        }

        $stmt->bindValue('user_id', $userId ?? null);
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
                'geometry' => json_decode($row['geo'], true),
                'data' => json_decode($row['data'], true)
            ];

        }

        return $this->render('front/index/index.html.twig', [
            'items' => $result
        ]);
    }
}
