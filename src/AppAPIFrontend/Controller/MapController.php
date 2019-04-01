<?php

namespace App\AppAPIFrontend\Controller;

use App\AppMain\Entity\Geospatial\Simplify;
use App\AppMain\Entity\Geospatial\StyleGroup;
use App\Services\Geometry\Utils;
use App\Services\Geospatial\Finder;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    protected $entityManager;
    protected $utils;
    protected $logger;
    protected $finder;
    protected $session;

    public function __construct(
        EntityManagerInterface $entityManager,
        Utils $utils,
        LoggerInterface $logger,
        Finder $finder,
        SessionInterface $session
    ) {
        $this->entityManager = $entityManager;
        $this->utils = $utils;
        $this->logger = $logger;
        $this->finder = $finder;
        $this->session = $session;
    }

    /**
     * @Route("/map", name="api.map")
     */
    public function index(Request $request): Response
    {
        $in = $request->query->get('in');
        $zoom = $request->query->get('zoom');
        $center = $request->query->get('c');
        $geo = $request->query->get('g');

        if (null === $in || null === $zoom) {
            return new JsonResponse(['Missing parameters'], 400);
        }

        $zoom = (float) $zoom;

        $simplify = $this->getDoctrine()->getRepository(Simplify::class)->findAll();

        $simplifyRanges = [];
        foreach ($simplify as $item) {
            $simplifyRanges[] = [
                'min_zoom' => $item->getMinZoom(),
                'max_zoom' => $item->getMaxZoom(),
                'tolerance' => $item->getTolerance(),
            ];
        }

        $simplifyTolerance = $this->utils->findTolerance($simplifyRanges, $zoom);
        $collectionId = $request->query->get('collection');

        $bbox = [];

        if ($collectionId) {
            $bbox = $this->findCollectionBbox($this->getUser()->getId(), $collectionId);
        }

        $geoObjects = $this->finder->find($zoom, $simplifyTolerance, $in, $this->getUser(), $collectionId);

        $stylesGroups = $this->getDoctrine()
            ->getRepository(StyleGroup::class)
            ->findAll()
        ;

        $styles = [];

        foreach ($stylesGroups as $stylesGroup) {
            $styles[$stylesGroup->getCode()] = $stylesGroup->getStyle();
        }

        $i = 0;

        $result = [];

        foreach ($geoObjects as $row) {
            ++$i;

            $geometry = json_decode($row['geometry'], true);
            $attributes = json_decode($row['attributes'], true);

            if ($row['uuid'] === $geo) {
                $row['style_base'] = 'on_dialog_line';
            }

            if (isset($row['entry'])) {
                $row['style_base'] = 'on_dialog_line';
            }

            if ('Градоустройствена единица' === $row['type_name']) {
                $attributes['_zoom'] = 17;
            }

            if (isset($attributes['_sca']) && 'Пресичания' === $attributes['_sca']) {
                $attributes['_zoom'] = 20;
            }

            $result[] = [
                'type' => 'Feature',
                'geometry' => $geometry,
                'properties' => [
                                    '_s1' => $row['style_base'],
                                    '_s2' => $row['style_hover'],
                                    'id' => $row['uuid'],
                                    'name' => $row['geo_name'],
                                    'type' => $row['type_name'],
                                ] + $attributes,
            ];
        }

        $this->logger->info('Map view', [
            'zoom' => $zoom,
            'center' => $center,
            'bbox' => $in,
            'simplify_tolerance' => $simplifyTolerance,
            'objects' => $i,
        ]);

        $this->session->set('center', $center);
        $this->session->set('zoom', $zoom);

        return new JsonResponse([
            'settings' => [
                'default_zoom' => 17,
                'styles' => $styles,
                'dialog' => [
                    1 => 'Искате ли да оцените избраното пресичане',
                    2 => 'Искате ли да оцените избрания тротоар',
                    3 => 'Искате ли да оцените избраната алея',
                ],
            ],
            'bbox' => $bbox,
            'objects' => $result,
        ]);
    }

    private function findCollectionBbox(int $userId, string $collectionUuid): array
    {
        /** @var Connection $conn */
        $conn = $this->getDoctrine()->getConnection();

        $stmt = $conn->prepare('
            WITH z AS (
            SELECT
                ST_Extent(gb.coordinates::geometry) as w
            FROM
                x_survey.gc_collection c
                    INNER JOIN
                x_survey.gc_collection_content cc ON c.id = cc.geo_collection_id
                    INNER JOIN
                x_geospatial.geo_object g ON cc.geo_object_id = g.id
                    INNER JOIN
                x_geometry.geometry_base gb ON g.id = gb.geo_object_id
            WHERE
                c.user_id = :user_id
                AND c.uuid = :collection_uuid
            )
            SELECT
                st_xmin(w) as xmin,
                st_xmax(w) as xmax,
                st_ymin(w) as ymin,
                st_ymax(w) as ymax,
                st_asgeojson(
                        st_makeenvelope(
                                st_xmin(w),
                                st_xmax(w),
                                st_ymin(w),
                                st_ymax(w)
                            )
                    ) as envelope
            FROM z
        ');

        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('collection_uuid', $collectionUuid);
        $stmt->execute();

        $row = $stmt->fetch();

        return [
            'xmin' => $row['xmin'],
            'xmax' => $row['xmax'],
            'ymin' => $row['ymin'],
            'ymax' => $row['ymax'],
            'rectangle' => json_decode($row['envelope']),
        ];
    }
}
