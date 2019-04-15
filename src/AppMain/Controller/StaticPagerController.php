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
     * @Route("/about", name="app.site.about", methods="GET")
     */
    public function about(): Response
    {
        $page = $this->getDoctrine()
            ->getRepository(StaticPage::class)
            ->findOneBy([
                'slug' => 'about'
            ]);

        return $this->render('front/static-page/index.html.twig', [
            'content' => $this->getContent($page)
        ]);
    }

    /**
     * @Route("/privacy-policy", name="app.site.privacy-policy", methods="GET")
     */
    public function privacyPolicy(): Response
    {
        $page = $this->getDoctrine()
            ->getRepository(StaticPage::class)
            ->findOneBy([
                'slug' => 'privacy-policy'
            ]);

        return $this->render('front/static-page/index.html.twig', [
            'content' => $this->getContent($page)
        ]);
    }

    /**
     * @Route("/terms-and-conditions", name="app.site.terms-and-conditions", methods="GET")
     */
    public function termsAndConditions(): Response
    {
        $page = $this->getDoctrine()
            ->getRepository(StaticPage::class)
            ->findOneBy([
                'slug' => 'terms-and-conditions'
            ]);

        return $this->render('front/static-page/index.html.twig', [
            'content' => $this->getContent($page)
        ]);
    }


    private function getContent(?StaticPage $staticPage): string
    {
        if ($staticPage === null) {
            return '';
        }

        return $this->markdown->text($staticPage->getContent());
    }
}
