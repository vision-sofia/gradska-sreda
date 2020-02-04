<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\StaticPage;
use App\Services\Markdown\MarkdownService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

class StaticPagerController extends AbstractController
{
    protected MarkdownService $markdown;
    protected AdapterInterface $cache;

    public function __construct(MarkdownService $markdown, AdapterInterface $cache)
    {
        $this->markdown = $markdown;
        $this->cache = $cache;
    }

    /**
     * @Route(
     *     "/site/{slug}",
     *     name="app.site.page",
     *     methods="GET",
     *     requirements={"slug": "terms-and-conditions|privacy-policy|about|open-data"}
     * )
     */
    public function show(string $slug): Response
    {
        $content = $this->cache->get('static-page-' . $slug, function (ItemInterface $item) use ($slug) {
            /** @var StaticPage|null $page */
            $page = $this->getDoctrine()
                ->getRepository(StaticPage::class)
                ->findOneBy([
                    'slug' => $slug
                ]);

            return $page === null ? '' : $this->markdown->text($page->getContent());
        });

        return $this->render('front/static-page/index.html.twig', [
            'content' => $content
        ]);
    }
}
