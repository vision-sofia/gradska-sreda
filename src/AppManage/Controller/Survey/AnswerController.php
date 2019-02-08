<?php

namespace App\AppManage\Controller\Survey;

use App\AppManage\Form\Survey\AnswerType;
use App\AppManage\Form\Survey\QuestionType;
use App\AppMain\Entity\Survey\Question\Answer;
use App\AppMain\Entity\Survey\Question\Question;
use App\Services\FlashMessage\FlashMessage;
use App\Services\Form\CsrfTokenValidator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/survey-system/questions", name="manage.survey-system.questions.answers.")
 */
class AnswerController extends AbstractController
{
    protected $flashMessage;
    protected $csrfTokenValidator;
    protected $translator;

    public function __construct(
        FlashMessage $flashMessage,
        CsrfTokenValidator $csrfTokenValidator,
        TranslatorInterface $translator)
    {
        $this->flashMessage = $flashMessage;
        $this->csrfTokenValidator = $csrfTokenValidator;
        $this->translator = $translator;
    }

    /**
     * @Route("/{question}/answers/new", name="new", methods="GET|POST")
     * @ParamConverter("question", class="App\AppMain\Entity\Survey\Question\Question", options={"mapping": {"question": "uuid"}})
     */
    public function new(Request $request, Question $question): Response
    {
        $answer = new Answer();
        $answer->setQuestion($question);
        $answer->setIsFreeAnswer(false);

        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($answer);
            $em->flush();

            $this->flashMessage->addSuccess(
                $this->translator->trans('answer', ['%count%' => 1]),
                $this->translator->trans('flash.edit.success')
            );

            return $this->redirectToRoute('manage.survey-system.questions.edit', ['question' => $question->getUuid()]);
        }

        return $this->render('manage/survey-system/answer/edit.html.twig', [
            'form'  => $form->createView(),
        ]);
    }

    /**
     * @Route("/{question}/answers/{answer}/edit", name="edit", methods="GET|POST")
     * @ParamConverter("question", class="App\AppMain\Entity\Survey\Question\Question", options={"mapping": {"question": "uuid"}})
     * @ParamConverter("answer", class="App\AppMain\Entity\Survey\Question\Answer", options={"mapping": {"answer": "uuid"}})
     */
    public function edit(Request $request, Question $question, Answer $answer): Response
    {
        $form = $this->createForm(AnswerType::class, $answer);
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

        return $this->render('manage/survey-system/answer/edit.html.twig', [
            'form'  => $form->createView(),
            'answer' => $question,
        ]);
    }

}
