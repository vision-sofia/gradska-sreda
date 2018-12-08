<?php

namespace App\AppMain\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function index(): Response
    {
        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare('
            SELECT 
                st_asgeojson(geography) as geo, 
                uuid 
            FROM 
                 x_geography.type_multiline
            LIMIT 100
');

        $stmt->execute();


        $result = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result[$row['uuid']] = json_decode($row['geo'], true)['coordinates'];
        }

        return new JsonResponse($result);

    }
}
