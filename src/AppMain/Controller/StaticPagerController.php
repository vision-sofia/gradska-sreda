<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\StaticPage;

use App\Services\Markdown\MarkdownService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class StaticPagerController extends AbstractController
{
    protected $markdown;

    public function __construct(MarkdownService $markdown)
    {
        $this->markdown = $markdown;
    }

    /**
     * @Route(
     *     "/site/{slug}",
     *     name="app.site.page",
     *     methods="GET",
     *     requirements={"slug": "terms-and-conditions|privacy-policy|about|open-data"}
     * )
     */
    public function termsAndConditions(string $slug): Response
    {
        /** @var StaticPage|null $page */
        $page = $this->getDoctrine()
            ->getRepository(StaticPage::class)
            ->findOneBy([
                'slug' => $slug
            ]);

        return $this->render('front/static-page/index.html.twig', [
            'content' => $page === null ? '' : $this->markdown->text($page->getContent())
        ]);
    }
}
