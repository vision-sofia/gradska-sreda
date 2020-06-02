<?php

namespace App\EventSubscriber;

use App\Event\Events;
use App\Services\Cache\Keys;
use App\Services\Geospatial\Style;
use App\Services\Geospatial\StyleBuilder\StyleBuilder;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StyleBuildSubscriber implements EventSubscriberInterface
{
    protected StyleBuilder $styleBuilder;
    protected AdapterInterface $cache;
    protected Style $styleService;

    public function __construct(
        StyleBuilder $styleBuilder,
        AdapterInterface $cache,
        Style $styleService
    ) {
        $this->styleBuilder = $styleBuilder;
        $this->cache = $cache;
        $this->styleService = $styleService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::STYLES_REBUILD => [
                ['rebuildStyles', 30],
                ['deleteCompiledStylesCache', 20],
                ['warmUpCompiledStylesCache', 10],
            ],
            Events::DYNAMIC_STYLE_TOUCH => [
                ['deleteDynamicStylesCache', 20],
                ['warmUpDynamicStylesCache', 10],
            ],
        ];
    }

    public function rebuildStyles(): void
    {
        // TODO: move to task queue
        $this->styleBuilder->build();
    }

    public function deleteCompiledStylesCache(): void
    {
        try {
            $this->cache->deleteItem(Keys::COMPILED_STYLES);
        } catch (InvalidArgumentException $e) {
        }
    }

    public function warmUpCompiledStylesCache(): void
    {
        $this->cache->get(Keys::COMPILED_STYLES, function () {
            return $this->styleService->getCompiledStyles();
        });
    }

    public function deleteDynamicStylesCache(): void
    {
        try {
            $this->cache->deleteItem(Keys::DYNAMIC_STYLES);
        } catch (InvalidArgumentException $e) {
        }
    }

    public function warmUpDynamicStylesCache(): void
    {
        $this->cache->get(Keys::DYNAMIC_STYLES, function () {
            return $this->styleService->getDynamicStyles();
        });
    }
}
