<?php

namespace App\AppManage\Controller\Survey;

use App\AppMain\Entity\Survey\Question\Question;
use App\AppManage\Form\Survey\QuestionType;
use App\Services\FlashMessage\FlashMessage;
use App\Services\Form\CsrfTokenValidator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/survey-system/questions", name="manage.survey-system.questions.")
 */
class QuestionController extends AbstractController
{
    protected $flashMessage;
    protected $csrfTokenValidator;
    protected $translator;

    public function __construct(
        FlashMessage $flashMessage,
        CsrfTokenValidator $csrfTokenValidator,
        TranslatorInterface $translator
    ) {
        $this->flashMessage = $flashMessage;
        $this->csrfTokenValidator = $csrfTokenValidator;
        $this->translator = $translator;
    }

    /**
     * @Route("", name="index", methods="GET")
     */
    public function index(): Response
    {
        $questions = $this->getDoctrine()
            ->getRepository(Question::class)
            ->findBy([], ['title' => 'ASC'])
        ;

        return $this->render('manage/survey-system/question/list.html.twig', [
            'questions' => $questions,
        ]);
    }

    /**
     * @Route("/new", name="new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $field = new Question();

        $form = $this->createForm(QuestionType::class, $field);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($field);
            $em->flush();

            $this->flashMessage->addSuccess(
                $this->translator->trans('question', ['%count%' => 1]),
                $this->translator->trans('flash.add.success')
            );

            return $this->redirectToRoute('manage.survey-system.questions.index');
        }

        return $this->render('manage/survey-system/question/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{question}/edit", name="edit", methods="GET|POST")
     * @ParamConverter("question", class="App\AppMain\Entity\Survey\Question\Question", options={"mapping": {"question": "uuid"}})
     */
    public function edit(Request $request, Question $question): Response
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
            $em->flush();

            $this->flashMessage->addSuccess(
                $this->translator->trans('question', ['%count%' => 1]),
                $this->translator->trans('flash.edit.success')
            );

            return $this->redirectToRoute('manage.survey-system.questions.edit', ['question' => $question->getUuid()]);
        }

        return $this->render('manage/survey-system/question/edit.html.twig', [
            'form' => $form->createView(),
            'item' => $question,
        ]);
    }

    /**
     * @Route("/{question}", name="delete", methods="DELETE")
     * @ParamConverter("question", class="App\AppMain\Entity\Survey\Question\Question", options={"mapping": {"question": "uuid"}})
     */
    public function delete(Question $question): Response
    {
        if ($this->csrfTokenValidator->isCsrfTokenValid('delete' . $question->getUuid())) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($question);
            $em->flush();

            $this->flashMessage->addSuccess(
                $this->translator->trans('question', ['%count%' => 1]),
                $this->translator->trans('flash.delete.success')
            );

            return $this->redirectToRoute('manage.survey-system.questions.index');
        }

        $this->flashMessage->addWarning(
            $this->translator->trans('survey', ['%count%' => 1]),
            $this->translator->trans('flash.csrf-token.invalid')
        );

        return $this->redirectToRoute('manage.survey-system.questions.index');
    }
}
