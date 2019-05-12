<?php

namespace App\AppAPIFrontend\Controller;

use App\AppMain\DTO\GeoObjectDTO;
use App\AppMain\Entity\Geospatial\Simplify;
use App\Services\Cache\Keys as CacheKeys;
use App\Services\GeoCollection\GeoCollection;
use App\Services\Geometry\Utils;
use App\Services\Geospatial\Finder;
use App\Services\Geospatial\Style;
use App\Services\Geospatial\StyleBuilder\StyleUtils;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

class MapController extends AbstractController
{
    protected $entityManager;
    protected $utils;
    protected $logger;
    protected $finder;
    protected $session;
    protected $geoCollection;
    protected $styleUtils;
    protected $cache;
    protected $styleService;
    protected $jsonUtils;

    public function __construct(
        EntityManagerInterface $entityManager,
        Utils $utils,
        LoggerInterface $logger,
        Finder $finder,
        SessionInterface $session,
        GeoCollection $geoCollection,
        StyleUtils $styleUtils,
        AdapterInterface $cache,
        Style $styleService,
        \App\Services\JsonUtils $jsonUtils
    )
    {
        $this->entityManager = $entityManager;
        $this->utils = $utils;
        $this->logger = $logger;
        $this->finder = $finder;
        $this->session = $session;
        $this->geoCollection = $geoCollection;
        $this->styleUtils = $styleUtils;
        $this->cache = $cache;
        $this->styleService = $styleService;
        $this->jsonUtils = $jsonUtils;
    }

    // TODO: refactor in to services

    /**
     * @Route("/map", name="api.map", methods="GET")
     */
    public function index(Request $request): Response
    {

        $in = $request->query->get('in');
        $zoom = $request->query->get('zoom');

        if (null === $in || null === $zoom) {
            return new JsonResponse(['Missing parameters'], 400);
        }

        $center = $request->query->get('c');
        $select = $request->query->get('select');


        $simplifyTolerance = $this->simplifyTolerance((int)$zoom);

        $collectionId = $request->query->get('collection');

        $boundingBox = null;
        /*
                if ($collectionId) {
                    $collectionBoundingBox = $this->geoCollection->findCollectionBoundingBox($this->getUser()->getId(), $collectionId);

                    if ($collectionBoundingBox->getYMin()) {
                        $boundingBox = Utils::buildBbox(
                            $collectionBoundingBox->getXMin(),
                            $collectionBoundingBox->getYMin(),
                            $collectionBoundingBox->getXMax(),
                            $collectionBoundingBox->getYMax()
                        );
                    }
                }
        */

        $geoObjects = $this->finder->find($zoom, $simplifyTolerance, $in, $this->getUser(), $collectionId);

        $userGeoCollection = $userSubmitted = $objects = [];
        $bbox = [];

        if ($this->getUser()) {
            $userSubmitted = $this->finder->userSubmitted($this->getUser()->getId(), $simplifyTolerance);
            $userGeoCollection = $this->finder->userGeoCollection($this->getUser()->getId(), $simplifyTolerance);

            $collectionBoundingBoxCollection = $this->geoCollection->findCollectionBoundingBoxByUser($this->getUser()->getId());

            foreach ($collectionBoundingBoxCollection as $collectionBoundingBox) {
                $geoObject = new GeoObjectDTO();
                $geoObject->geometry = $collectionBoundingBox->getPolygon();
                $geoObject->style_base = 'gc_bbox';
                $geoObject->style_hover = 'gc_bbox';
                $geoObject->properties = '{}';

                $bbox[] = $geoObject;
            }
        }

        $dynamicStyles = $this->cache->get(CacheKeys::DYNAMIC_STYLES, function () {
            return $this->styleService->getDynamicStyles();
        });

        $styleGroups = $this->cache->get(CacheKeys::COMPILED_STYLES, function () {
            return $this->styleService->getCompiledStyles();
        });

        if ($dynamicStyles) {
            $this->styleUtils->setDynamicStyles($dynamicStyles);
        }

        if ($styleGroups) {
            $this->styleUtils->setStaticStyles($styleGroups);
        }

        foreach ($geoObjects as $row) {
            $objects[] = $this->process($row, $styleGroups, $this->styleUtils);
        }

        foreach ($userGeoCollection as $row) {
            $objects[] = $this->process($row, $styleGroups, $this->styleUtils);
        }

        foreach ($bbox as $row) {
            $objects[] = $this->process($row, $styleGroups, $this->styleUtils);
        }

        foreach ($userSubmitted as $row) {
            $objects[] = $this->process($row, $styleGroups, $this->styleUtils);
        }

        if ($select) {
            $geo = $this->finder->findSelected($select);

            if ($geo) {
                $objects[] = $this->process($geo, $styleGroups, $this->styleUtils);
            }
        }

        $this->logger->info('Map view', [
            'mem' => round(memory_get_usage() / 1024 / 1024, 2),
            'zoom' => $zoom,
            'center' => $center,
            'bbox' => $in,
            'simplify_tolerance' => $simplifyTolerance,
            'objects' => 0,
        ]);

        $this->session->set('center', $center);
        $this->session->set('zoom', $zoom);

        $settings = [
            'settings' => [
                'default_zoom' => 17,
                'styles' => $styleGroups,
                'dialog' => [
                    1 => 'Искате ли да оцените избраното пресичане',
                    2 => 'Искате ли да оцените избрания тротоар',
                    3 => 'Искате ли да оцените избраната алея',
                ],
            ]
        ];

        $content = $this->jsonUtils->concatString($settings, 'objects', $this->jsonUtils->joinArray($objects));

        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    private function process(GeoObjectDTO $row, &$styles, StyleUtils $styleUtils): string
    {
        $properties = json_decode($row->properties, false);

        $properties->_s1 = $row->style_base ?? null;
        $properties->_s2 = $row->style_hover ?? null;
        $properties->name = $row->geo_name ?? null;
        $properties->type = $row->type_name ?? null;
        $properties->id = $row->uuid ?? null;

        $s = $styleUtils->inherit('line', $properties, $row->style_base, $row->style_hover);

        if (isset($s['base_style_code'])) {
            $properties->_s1 = $s['base_style_code'];
            $styles[$s['base_style_code']] = $s['base_style_content'];
        }

        if (isset($s['hover_style_code'])) {
            $properties->_s2 = $s['hover_style_code'];
            $styles[$s['hover_style_code']] = $s['hover_style_content'];
        }

        /*
        if ('Градоустройствена единица' === $row['type_name']) {
            $attributes['_zoom'] = 17;
        }
        */
        /*
                if (isset($attributes['_sca']) && 'Пресичания' === $attributes['_sca']) {
                    $attributes['_zoom'] = 20;
                }
        */

        return $this->jsonUtils->concatString([
            'type' => 'Feature',
            'properties' => $properties,
        ],
            'geometry',
            $row->geometry
        );
    }

    private function simplifyTolerance(int $zoom)
    {
        $simplifyTolerance = $this->cache->get('simplify-tolerance-' . $zoom, function (ItemInterface $item) use ($zoom) {
            /** @var Simplify[] $simplify */
            $simplifySet = $this->getDoctrine()->getRepository(Simplify::class)->findAll();

            $simplifyRanges = [];
            foreach ($simplifySet as $simplify) {
                $simplifyRanges[] = [
                    'min_zoom' => $simplify->getZoom()->getEnd(),
                    'max_zoom' => $simplify->getZoom()->getStart(),
                    'tolerance' => $simplify->getTolerance(),
                ];
            }

            return $this->utils->findTolerance($simplifyRanges, $zoom);
        });

        return $simplifyTolerance;
    }
}
