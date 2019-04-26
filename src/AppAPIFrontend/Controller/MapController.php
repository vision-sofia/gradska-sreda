<?php

namespace App\AppAPIFrontend\Controller;

use App\AppMain\DTO\BoundingBoxDTO;
use App\AppMain\Entity\Geospatial\Simplify;
use App\AppMain\Entity\Geospatial\StyleCondition;
use App\AppMain\Entity\Geospatial\StyleGroup;
use App\Services\GeoCollection\GeoCollection;
use App\Services\Geometry\Utils;
use App\Services\Geospatial\Finder;
use App\Services\Geospatial\StyleBuilder\StyleUtils;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PDO;
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
    protected $geoCollection;
    protected $styleUtils;

    public function __construct(
        EntityManagerInterface $entityManager,
        Utils $utils,
        LoggerInterface $logger,
        Finder $finder,
        SessionInterface $session,
        GeoCollection $geoCollection,
        StyleUtils $styleUtils
    ) {
        $this->entityManager = $entityManager;
        $this->utils = $utils;
        $this->logger = $logger;
        $this->finder = $finder;
        $this->session = $session;
        $this->geoCollection = $geoCollection;
        $this->styleUtils = $styleUtils;
    }

    /**
     * @Route("/map", name="api.map", methods="GET")
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

        $zoom = (float)$zoom;

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

        $boundingBox = null;

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


        $geoObjects = $this->finder->find($zoom, $simplifyTolerance, $in, $this->getUser(), $collectionId);

        $stylesGroups = $this->getDoctrine()
            ->getRepository(StyleGroup::class)
            ->findAll();

        $styles = [];

        foreach ($stylesGroups as $stylesGroup) {
            $styles[$stylesGroup->getCode()] = $stylesGroup->getStyle();
        }

        $userGeoCollection = $userSubmitted = $result = [];

        if ($this->getUser()) {
            $userSubmitted = $this->finder->userSubmitted($this->getUser()->getId(), $simplifyTolerance);
            $userGeoCollection = $this->finder->userGeoCollection($this->getUser()->getId(), $simplifyTolerance);
        }

        /** @var StyleCondition[] $dynamicStyles */
        $dynamicStyles = $this->getDoctrine()->getRepository(StyleCondition::class)->findBy([
            'isDynamic' => true
        ]);

        $ds = [];

        foreach ($dynamicStyles as $dynamicStyle) {
            $ds[$dynamicStyle->getAttribute()][$dynamicStyle->getValue()]['base_style'] = $dynamicStyle->getBaseStyle();
            $ds[$dynamicStyle->getAttribute()][$dynamicStyle->getValue()]['hover_style'] = $dynamicStyle->getHoverStyle();
        }

        $this->styleUtils->setDynamicStyles($ds);
        $this->styleUtils->setStaticStyles($styles);

        foreach ($geoObjects as $row) {
            $result[] = $this->process($row, $styles, $this->styleUtils, $ds);
        }

        foreach ($userSubmitted as $row) {
            $result[] = $this->process($row, $styles, $this->styleUtils, $ds);
        }

        foreach ($userGeoCollection as $row) {
            $result[] = $this->process($row, $styles, $this->styleUtils, $ds);
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
            'bbox' => $boundingBox,
            'objects' => $result,
        ]);
    }

    private function process($row, &$styles, StyleUtils $styleUtils, $geoCollectionUuid = null): array
    {
        $geometry = json_decode($row['geometry'], true);
        $attributes = json_decode($row['attributes'], true);

        $s = $styleUtils->inherit('line', $attributes, $row['style_base'], $row['style_hover']);

        if (isset($s['base_style_code'])) {
            $row['style_base'] = $s['base_style_code'];
            $styles[$s['base_style_code']] = $s['base_style_content'];
        }

        if (isset($s['hover_style_code'])) {
            $row['style_hover'] = $s['hover_style_code'];
            $styles[$s['hover_style_code']] = $s['hover_style_content'];
        }

        if (isset($row['geo_collection_uuid'], $geoCollectionUuid, $styles[$row['style_base']]) && $row['geo_collection_uuid'] === $geoCollectionUuid) {
            $newBaseStyle = $row['style_base'] . '-zz';
            $styles[$newBaseStyle] = array_merge($styles[$row['style_base']], ['color' => '#FF00FF']);
            $row['style_base'] = $newBaseStyle;
        }

        /*
        if ('Градоустройствена единица' === $row['type_name']) {
            $attributes['_zoom'] = 17;
        }

        if (isset($attributes['_sca']) && 'Пресичания' === $attributes['_sca']) {
            $attributes['_zoom'] = 20;
        }
        */

        return [
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
}
