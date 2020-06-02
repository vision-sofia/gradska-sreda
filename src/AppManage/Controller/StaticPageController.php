<?php

namespace App\AppManage\Controller;

use App\AppMain\Entity\StaticPage;
use App\AppManage\Form\Type\StaticPageType;
use App\Services\FlashMessage\FlashMessage;
use App\Services\Markdown\MarkdownInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/static-pages", name="manage.static-pages.")
 */
class StaticPageController extends AbstractController
{
    protected $flashMessage;
    protected $translator;
    protected $markdown;

    public function __construct(
        FlashMessage $flashMessage,
        TranslatorInterface $translator,
        MarkdownInterface $markdown
    ) {
        $this->flashMessage = $flashMessage;
        $this->translator = $translator;
        $this->markdown = $markdown;
    }

    /**
     * @Route("", name="list", methods="GET")
     */
    public function list(): Response
    {
        $pages = $this->getDoctrine()
            ->getRepository(StaticPage::class)
            ->findBy([], [
            ])
        ;

        return $this->render('manage/static-page/list.html.twig', [
            'pages' => $pages,
        ]);
    }

    /**
     * @Route("/{slug}", name="edit", methods={"GET", "POST"})
     * @ParamConverter("staticPage", class="App\AppMain\Entity\StaticPage", options={"mapping": {"slug": "slug"}})
     */
    public function edit(Request $request, StaticPage $staticPage): Response
    {
        $form = $this->createForm(StaticPageType::class, $staticPage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->flashMessage->addSuccess(
                '',
                $this->translator->trans('flash.edit.success')
            );

            return $this->redirectToRoute('manage.static-pages.edit', ['slug' => $staticPage->getSlug()]);
        }

        $preview = $this->markdown->text($staticPage->getContent());

        return $this->render('manage/static-page/edit.html.twig', [
            'staticPage' => $staticPage,
            'form' => $form->createView(),
            'preview' => $preview,
        ]);
    }
}
