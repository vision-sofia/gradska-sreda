<?php

namespace App\AppAPIFrontend\Controller;

use App\AppMain\DTO\SurveyGeoObjectDTO;
use App\AppMain\Entity\Geospatial\Simplify;
use App\AppMain\Entity\Survey\Survey\Survey;
use App\Services\Cache\Keys as CacheKeys;
use App\Services\GeoCollection\GeoCollection;
use App\Services\Geometry\Utils;
use App\Services\Geospatial\Finder;
use App\Services\Geospatial\Style;
use App\Services\Geospatial\StyleBuilder\StyleUtils;
use App\Services\JsonUtils;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
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
        JsonUtils $jsonUtils
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

        $survey = $this->getDoctrine()->getRepository(Survey::class)->findOneBy([
            'isActive' => true
        ]);

        if (!$survey) {
            return new JsonResponse();
        }

        $mapCenter = $request->query->get('c');
        $selectedObject = $request->query->get('select');

        $simplifyTolerance = $this->simplifyTolerance((int)$zoom);

        $geoObjects = $this->finder->find($zoom, $simplifyTolerance, $in, $survey->getId());

        $userGeoCollection = $userSubmitted = $objects = [];
        $boundingBoxes = [];

        if ($this->getUser()) {
            $userSubmitted = $this->finder->userSubmitted($this->getUser()->getId(), $simplifyTolerance);
            $userGeoCollection = $this->finder->userGeoCollection($this->getUser()->getId(), $simplifyTolerance);

            $collectionBoundingBoxCollection = $this->geoCollection->findCollectionBoundingBoxByUser($this->getUser()->getId());

            foreach ($collectionBoundingBoxCollection as $collectionBoundingBox) {
                $geoObject = new SurveyGeoObjectDTO();
                $geoObject->geometry = $collectionBoundingBox->getPolygon();
                $geoObject->base_style = 'gc_bbox';
                $geoObject->hover_style = 'gc_bbox';
                $geoObject->properties = '{}';

                $boundingBoxes[] = $geoObject;
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

        foreach ($boundingBoxes as $row) {
            $objects[] = $this->process($row, $styleGroups, $this->styleUtils);
        }

        foreach ($userSubmitted as $row) {
            $objects[] = $this->process($row, $styleGroups, $this->styleUtils);
        }

        if ($selectedObject) {
            $geo = $this->finder->findSelected($selectedObject);

            if ($geo) {
                $objects[] = $this->process($geo, $styleGroups, $this->styleUtils);
            }
        }

        $this->logger->info('Map view', [
            'mem' => round(memory_get_usage() / 1024 / 1024, 2),
            'zoom' => $zoom,
            'center' => $mapCenter,
            'bbox' => $in,
            'simplify_tolerance' => $simplifyTolerance,
            'objects' => 0,
        ]);

        $this->session->set('center', $mapCenter);
        $this->session->set('zoom', $zoom);

        $settings = [
            'settings' => [
                'default_zoom' => 17,
                'styles' => $styleGroups,
                'dialog' => [
                    1 => '',
                    2 => '',
                    3 => '',
                ],
            ]
        ];

        $content = $this->jsonUtils->concatString($settings, 'objects', $this->jsonUtils->joinArray($objects));

        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    private function process(SurveyGeoObjectDTO $row, &$styles, StyleUtils $styleUtils): string
    {
        $properties = json_decode($row->properties, false);
        $properties->_s1 = $row->base_style ?? null;
        $properties->_s2 = $row->hover_style ?? null;
        $properties->name = $row->geo_name ?? null;
        $properties->type = $row->type_name ?? null;
        $properties->id = $row->uuid ?? null;

        // TODO: add _zoom in 'properties' field in database
        if (isset($properties->_sca) && 'Пресичания' === $properties->_sca) {
            $properties->_zoom = 20;
        }

        $s = $styleUtils->inherit('line', $properties, $row->base_style, $row->hover_style);

        if (isset($s['base_style_code'])) {
            $properties->_s1 = $s['base_style_code'];
            $styles[$s['base_style_code']] = $s['base_style_content'];
        }

        if (isset($s['hover_style_code'])) {
            $properties->_s2 = $s['hover_style_code'];
            $styles[$s['hover_style_code']] = $s['hover_style_content'];
        }

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
        $simplifyTolerance = $this->cache->get(
            CacheKeys::SIMPLIFY_TOLERANCE .
            CacheKeys::VALUE_SEPARATOR .
            $zoom,
            function () use ($zoom) {
                /** @var Simplify[] $simplify */
                $simplifySet = $this->getDoctrine()
                    ->getRepository(Simplify::class)
                    ->findAll();

                $ranges = [];

                foreach ($simplifySet as $simplify) {
                    $ranges[] = [
                        'min_zoom' => $simplify->getZoom()->getEnd(),
                        'max_zoom' => $simplify->getZoom()->getStart(),
                        'tolerance' => $simplify->getTolerance(),
                    ];
                }

                return $this->utils->findTolerance($ranges, $zoom);
            });

        return $simplifyTolerance;
    }
}
