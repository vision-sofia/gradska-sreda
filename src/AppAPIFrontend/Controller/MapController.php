<?php

namespace App\AppAPIFrontend\Controller;

use App\AppMain\Entity\Geospatial\Simplify;
use App\AppMain\Entity\Geospatial\StyleGroup;
use App\Services\Geometry\Utils;
use App\Services\Geospatial\Finder;
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
    protected $finder;

    public function __construct(
        EntityManagerInterface $entityManager,
        Utils $utils,
        LoggerInterface $logger,
        Finder $finder
    ) {
        $this->entityManager = $entityManager;
        $this->utils = $utils;
        $this->logger = $logger;
        $this->finder = $finder;
    }

    /**
     * @Route("/map", name="api.map")
     */
    public function index(Request $request): Response
    {
        $in = $request->query->get('in');
        $zoom = $request->query->get('zoom');

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

        $geoObjects = $this->finder->find($zoom, $simplifyTolerance, $in, $this->getUser(), $collectionId);

        $stylesGroups = $this->getDoctrine()
            ->getRepository(StyleGroup::class)
            ->findAll();

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
            'bbox' => $in,
            'simplify_tolerance' => $simplifyTolerance,
            'objects' => $i,
        ]);

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
            'objects' => $result,
        ]);
    }
}
